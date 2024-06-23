<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class userAuthController extends Controller
{


    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"userAuth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="User's name"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="User's email"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="User's password"
     *                 ),
     *                 @OA\Property(
     *                     property="phone",
     *                     type="string",
     *                     description="User's phone"
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="User's image"
     *                 ),
     *                 @OA\Property(
     *                     property="userType",
     *                     type="string",
     *                     description="userType choose between ['user','employee','admin']"
     *                 ),
    *     @OA\Property(
    *         property="comment",
    *         type="string",
    *         description="to access image use https://mahllola.online/public/image  example : https://mahllola.online/public/storage/services_folder/ttyVNuauz67kqXX40jyewMwh3DWpdFjjyJ0pIiPd.jpg "
    *     ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="User registered successfully"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors"
     *     )
     * )
     */
    public function register(Request $request)
    {
        $validatedData = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:8',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:users',
            'userType' => 'required|in:user,admin,employee',
        ];
        if ($request->hasFile('image')) {
            $validatedData['image'] = 'sometimes|file|mimes:jpeg,png,jpg,gif|max:2048';
            // Validate the request data
            $validatedData = Validator::make($request->all(), $validatedData);
            if ($validatedData->fails()) {
                return response()->json(['status' => false, 'message' => $validatedData->errors()], 401);
            }
            // Store the image in the 'services_images' directory on the 'public' disk
            $newImage = $request->file('image')->store('users_folder', 'public');

            // Get the URL to the stored image
            $imageUrl = Storage::url($newImage);

            // store copy of image in public
            Artisan::call('storage:link');

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'image' => $imageUrl,
                'userType' => $request->userType,
            ]);
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'image' => null,
                'location' => $request->location,
                'userType' => $request->userType,
            ]);
        }
        return response()->json(['status' => true, 'message' => 'User registered successfully', 'user' => $user], 200);
    }


/**
 * @OA\Post(
 * path="/api/login",
 * summary="Authenticate user and generate token",
 * tags={"userAuth"},
 * @OA\Parameter(
 *     name="email",
 *     in="query",
 *     description="User's email",
 *     required=true,
 *     @OA\Schema(
 *         type="string",
 *         example="ahmed@gmail.com"
 *     )
 * ),
 * @OA\Parameter(
 *     name="password",
 *     in="query",
 *     description="User's password",
 *     required=true,
 *     @OA\Schema(
 *         type="string",
 *         example="Am123456"
 *     )
 * ),
 * @OA\Response(
 *     response="200",
 *     description="Login successful"
 * ),
 * @OA\Response(
 *     response="401",
 *     description="Invalid credentials"
 * )
 * )
 */

     public function login(Request $request)
     {
         // Validate the request data
         $validator = Validator::make($request->all(), [
             'email' => 'required|string|email',
             'password' => 'required|string',
         ]);
     
         if ($validator->fails()) {
             return response()->json([
                 'status' => 'error',
                 'message' => 'Validation error',
                 'errors' => $validator->errors(),
             ], 401);
         }
     
         // Get the credentials from the request
         $credentials = $request->only('email', 'password');
     
         // Attempt to log in the user
         if (!$token = Auth::attempt($credentials)) {
             return response()->json([
                 'status' => 'error',
                 'message' => 'Unauthorized. Incorrect email or password.',
             ], 401);
         }
     
         // Get the authenticated user
         $user = Auth::user();

         if($user->userType == 'user'){
            return response()->json([
                'status' => 'success',
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ], 200);
         }

         elseif($user->userType == 'employee'){
            $employeeData = Employee::where('user_id',$user->id)->first(); 

            return response()->json([
                'status' => 'success',
                'user' => $user,
                'employee' => $employeeData,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ], 200);
         }
         
     }
     



    /**
     * @OA\Post(
     *     path="/api/editUserProfile/{user_id}",
     *     summary="edit to user profile",
     *     tags={"userAuth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="name"
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="Image file"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="update Profile successfully"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors"
     *     )
     * )
     */

        // edit on user profile function

        public function editUserProfile(Request $request,$id)
        {

            try{
            $validatedData = [];

            $user = User::find($id);


            // return message failed if validation is false
            if ($request->hasFile('image') && $request->has('name') ) {
                $validatedData['image'] = 'file|mimes:jpeg,png,jpg,gif|max:2048';
                $validatedData['name'] = 'required|string|max:255';

                // Validate the request data
                $validatedData = Validator::make($request->all(), $validatedData);
                if ($validatedData->fails()) {
                    return response()->json(['status' => false, 'message' => $validatedData->errors()], 401);
                }


                // Get the old image URL
                $oldImageUrl = $user->image;

                // Extract the old image path from the URL
                $oldImagePath = str_replace('/storage', 'public', $oldImageUrl);

                // Delete the old image file from storage
                if (Storage::exists($oldImagePath)) {
                    Storage::delete($oldImagePath);
                }

                // Store the image in the 'services_images' directory on the 'public' disk
                $newImage = $request->file('image')->store('users_folder','public');

                // Get the URL to the stored image
                $imageUrl = Storage::url($newImage);

                // store copy of image in public
                Artisan::call('storage:link');

                // update data
                $user->update([
                    'name' => $request->name,
                    'email' => $user->email,
                    'password' => $user->password,
                    'phone' => $user->phone,
                    'image' => $imageUrl,
                    'userType' => $user->userType,
                ]);
            }

            elseif($request->has('name')){
                $validatedData['name'] = 'required|string|max:255';

                // Validate the request data
                $validatedData = Validator::make($request->all(), $validatedData);
                if ($validatedData->fails()) {
                    return response()->json(['status' => false, 'message' => $validatedData->errors()], 401);
                }

                // update data
                $user->update([
                    'name' => $request->name,
                    'email' => $user->email,
                    'password' => $user->password,
                    'phone' => $user->phone,
                    'image' => $user->image,
                    'userType' => $user->userType,
                ]);

            }

            return response()->json([
                'status' => 'true',
                'message' => 'update Profile done',
                'user' => $user
            ],200);
            }

            catch(Throwable $e){
                return $e;
            }

        }



    /**
     * @OA\Get(
     * path="/api/user",
     * summary="Get logged-in user details",
     * tags={"userAuth"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(response="200", description="Success"),
     * security={{"bearerAuth":{}}}
     * )
     */
    public function getUserDetails(Request $request)
    {
        $user = $request->user();
        return response()->json(['user' => $user], 200);
    }

    
    /**
     * @OA\Get(
     * path="/api/allUser",
     * tags={"userAuth"},
     *  security={{"bearerAuth":{}}},
     * summary="Get display all user details",
     * @OA\Response(response="200", description="Success"),

     * )
     */
    public function allUser()
    {
        $user = User::all();
        if($user){
            return response()->json(['user' => $user], 200);
        }
        else{
            return response()->json(['message' => "No user found"], 401);
        }

    }

    /**
     * @OA\Get(
     *  path="/api/user/notifications/{id}",
     *  summary="show all notifications to user",
     *  tags={"userAuth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(
     *             type="integer",
     *         ),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *     ),
     *
     * )
     */

    // return user notifications

    public function notifications($user_id)
    {
        $user = User::find($user_id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        $notifications = $user->notifications->map(function ($notification) {
            return $notification->data;
        });
    
        if ($notifications->isEmpty()) {
            return response()->json([
                'status' => 'true',
                'message' => 'No notifications found'
            ], 200);
        }
    
        return response()->json([
            'status' => 'true',
            'message' => $notifications
        ], 200);
    }

    // logout function

    /**
     * @OA\Get(
     * path="/api/logout",
     * summary="user logout",
     * tags={"userAuth"},
     *  security={{"bearerAuth":{}}},
     * @OA\Response(response="200", description="Success"),
     * security={{"bearerAuth":{}}}
     * )
     */

     public function logout()
     {
         Auth::logout();
         return response()->json([
             'status' => 'success',
             'message' => 'Successfully logged out',
         ],200);
     }


    // change password

    // public function changePassword(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'current_password' => 'required|string',
    //         'new_password' => 'required|string|min:8|confirmed',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['status' => false, 'message' => $validator->errors()], 401);
    //     }

    //     $user = Auth::user();

    //     if (!Hash::check($request->current_password, $user->password)) {
    //         return response()->json(['status' => false, 'message' => 'Current password is incorrect'], 401);
    //     }

    //     $user->password = Hash::make($request->new_password);
    //     $user->save();

    //     return response()->json(['status' => true, 'message' => 'Password changed successfully'], 200);
    // }


}
