<?php

namespace App\Http\Controllers;

use App\Models\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\Throw_;
use Throwable;

class servicesController extends Controller
{

    // display all services


    /**
     * @OA\Get(
     *     path="/api/services",
     *     summary="Show all services",
     *     tags={"services"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="services", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function index()
    {
        try{
        $services=Services::all();

        if($services->count() != 0){
            return response()->json([
                'status' => true,
                'message' => 'display all services done',
                'services' => $services
            ],200);
        }

        else{
            return response()->json([
                'status' => false,
                'message' => 'not found',
            ],401);
        }



        }

        catch(Throwable $e){
            return $e;
        }

    }

    // add new service

    /**
     * @OA\Post(
     *     path="/api/services",
     *     summary="create a new service",
     *     tags={"services"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="name of service"
     *                 ),
     *                 @OA\Property(
     *                     property="desc",
     *                     type="string",
     *                     description="service description"
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="service image"
     *                 ),
    *     @OA\Property(
    *         property="comment",
    *         type="string",
    *         description="to access image use https://mahllola.online/public/image  example : https://mahllola.online/public/storage/services_folder/ttyVNuauz67kqXX40jyewMwh3DWpdFjjyJ0pIiPd.jpg"
    *     )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="add new service successfully"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors"
     *     )
     * )
     */
    public function store(Request $request)
    {

    // validation to input
    $validatedData = [
        'name' => 'required|string|max:255',
        'desc' => 'required|string',
        ];

        // upload image
        try{

            if ($request->hasFile('image')) {
                $validatedData['image'] = 'file|mimes:jpeg,png,jpg,gif|max:2048';
                // Validate the request data
                $validatedData = Validator::make($request->all(), $validatedData);
                if ($validatedData->fails()) {
                    return response()->json(['status' => false, 'message' => $validatedData->errors()], 401);
                }

                // Store the image in the 'services_images' directory on the 'public' disk
                 $newImage = $request->file('image')->store('services_folder','public');

                // Get the URL to the stored image
                $imageUrl = Storage::url($newImage);

                // store copy of image in public
                Artisan::call('storage:link');

                // store new service

               $service=Services::create([
                'name' => $request->name,
                'desc' => $request->desc,
                'image' =>$imageUrl,
               ]);
            }
            else {
                $service=Services::create([
                    'name' => $request->name,
                    'desc' => $request->desc,
                    'image' => null
                   ]);
            }

            return response()->json([
                'status' => 'true',
                'message' => 'Add new service done',
                'service' => $service,
            ],200);

    }


    catch(Throwable $e){
        return $e;
    }

    }

        /**
     * @OA\Get(
     *     path="/api/services/{service_id}",
     *     summary="Get service by id",
     *     tags={"services"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="service_id",
     *         in="path",
     *         required=true,
     *         description="service id",
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

    // show by id
    public function show($id)
    {
        try{
        $service = Services::find($id);
        if($service){
            return response()->json([
                'status' => 'true',
                'message' => 'show service done',
                'services' => $service
            ],200);
        }
        return response()->json(['message' => 'not found'], 401);
        }

        catch(Throwable $e){
            return $e;
        }
    }



        /**
     * @OA\Post(
     *     path="/api/services/{service_id}",
     *     summary="edit to services",
     *     tags={"services"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="service_id",
     *         in="path",
     *         required=true,
     *         description="ID of the service",
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
     *                     description="service name"
     *                 ),
    *                 @OA\Property(
     *                     property="desc",
     *                     type="string",
     *                     description="service description"
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
     *         description="update service successfully"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors"
     *     )
     * )
     */

    // edit on service function
    public function update(Request $request,$id)
    {

        try{
        // validation to input
        $validator = Validator::make($request->all(),[
        'name' => 'required|string|max:255',
        'desc' => 'required|string',
        'image' =>'file|max:3072|mimes:jpeg,png,jpg,gif',
        ]);

        // return message failed if validation is false
        if($validator->fails()){
            return response()->json([
            'status' => 'false',
            'message' => $validator->errors()
            ],401);
            }

        $service = Services::find($id);

        // If a new image is uploaded
        if ($request->hasFile('image')) {
            // Get the old image URL
            $oldImageUrl = $service->image;

            // Extract the old image path from the URL
            $oldImagePath = str_replace('/storage', 'public', $oldImageUrl);

            // Delete the old image file from storage
            if (Storage::exists($oldImagePath)) {
                Storage::delete($oldImagePath);
            }

            // Store the image in the 'services_images' directory on the 'public' disk
            $newImage = $request->file('image')->store('services_folder','public');

            // Get the URL to the stored image
            $imageUrl = Storage::url($newImage);

            // store copy of image in public
            Artisan::call('storage:link');

        }


        else{
            $imageUrl = $service->image;
        }

        // update data
        $service->update([
            'name' => $request->name,
            'desc' => $request->desc,
            'image' => $imageUrl
        ]);

        return response()->json([
            'status' => 'true',
            'message' => 'update service done',
            'services' => $service
        ],200);
        }

        catch(Throwable $e){
            return $e;
        }

    }


    /**
 * @OA\Delete(
 *     path="/api/services/{service_id}",
 *     summary="Delete an service",
 *     description="Delete service by ID",
 *     tags={"services"},
*     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="service_id",
 *         in="path",
 *         description="ID of the service to delete",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="service deleted successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="service not found"
 *     )
 * )
 */

    // delete by id
    public function destroy($id)
    {
        try{
        $service = Services::find($id);
        if($service){
        // Extract the image URL
        $imageUrl = $service->image;

        // Extract the image path from the URL
        $imagePath = str_replace('/storage', 'public', $imageUrl);

        // Delete the image file from storage
        if (Storage::exists($imagePath)) {
            Storage::delete($imagePath);
        }
            // delete all data
            $service->delete();
            return response()->json([
                'status' => 'true',
                'message' => 'delete service done',
            ],200);
        }
        return response()->json(['message' => 'not found'], 401);
        }

        catch(Throwable $e){
            return $e;
        }
    }


}
