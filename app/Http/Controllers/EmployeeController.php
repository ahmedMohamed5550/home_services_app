<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeWork;
use App\Models\User;
use App\Services\FeedbackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class EmployeeController extends Controller
{

    protected $feedbackService;

    public function __construct(FeedbackService $feedbackService)
    {
        $this->feedbackService = $feedbackService;
    }

    /**
     * @OA\Post(
     *     path="/api/employee/employeeCompleteData",
     *     summary="Add details to employee",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="desc",
     *                     type="string",
     *                     description="Description of the employee"
     *                 ),
     *                 @OA\Property(
     *                     property="location",
     *                     type="string",
     *                     description="location of the employee"
     *                 ),
     *                 @OA\Property(
     *                     property="imageSSN",
     *                     type="string",
     *                     format="binary",
     *                     description="صورة الباطاقة"
     *                 ),
     *                 @OA\Property(
     *                     property="livePhoto",
     *                     type="string",
     *                     format="binary",
     *                     description="صورة لايف"
     *                 ),
     *                 @OA\Property(
     *                     property="nationalId",
     *                     type="string",
     *                     description=" الرقم القومي"
     *                 ),
     *                 @OA\Property(
     *                     property="min_price",
     *                     type="integer",
     *                     description="Minimum price"
     *                 ),
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="integer",
     *                     description="User ID"
     *                 ),
     *                 @OA\Property(
     *                     property="service_id",
     *                     type="integer",
     *                     description="Service ID"
     *                 ),
     *                 @OA\Property(
     *                     property="works[0][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 1",
     *                      nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="works[1][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 2",
     *                      nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="works[2][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 3",
     *                      nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="works[3][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 4",
     *                      nullable=true,
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee details add successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="successfully"
     *             ),
     *             @OA\Property(
     *                 property="employee",
     *                 type="object"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="object"
     *             )
     *         )
     *     )
     * )
     */
    public function employeeCompleteData(Request $request)
    {

        $validatedData = Validator::make($request->all(), [
            'desc' => 'required|string',
            'location' => 'required|string',
            'imageSSN' => 'file|mimes:jpeg,png,jpg,gif',
            'livePhoto' => 'file|mimes:jpeg,png,jpg,gif',
            'nationalId' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:13',
            'min_price' => 'required',
            'user_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'works' => 'nullable|array|max:4',
            'works.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);
        if ($validatedData->fails()) {
            return response()->json(['status' => false, 'message' => $validatedData->errors()], 401);
        }

        // Store the national image ssn in the 'employees_ssn' directory on the 'public' disk
        $newImageSsn = $request->file('imageSSN')->store('employees_ssn', 'public');
        // Get the URL to the stored image
        $imageSsnUrl = Storage::url($newImageSsn);

        // store live photo
        $newImageLive = $request->file('livePhoto')->store('employees_live_photo', 'public');
        $imageLive = Storage::url($newImageLive);


        $employee = Employee::create([
            'desc' => $request->desc,
            'location' => $request->location,
            'imageSSN' => $imageSsnUrl,
            'livePhoto' => $imageLive,
            'nationalId' => $request->nationalId,
            'min_price' => $request->min_price,
            'user_id' => $request->user_id,
            'service_id' => $request->service_id
        ]);


        if ($request->has('works')) {
            $works = $request->works;

            // Ensure there are exactly 4 entries, filling with null values if necessary
            $works = array_pad($works, 4, ['image' => null]);

            foreach ($works as $work) {
                $workImageUrl = null;

                if (isset($work['image']) && $work['image']) {
                    $workImage = $work['image'];
                    $workImagePath = $workImage->store('employee_works', 'public');
                    $workImageUrl = Storage::url($workImagePath);
                }

                EmployeeWork::create([
                    'user_id' => $request->user_id,
                    'image_url' => $workImageUrl,
                ]);


            }
        }


        else{
            for($i=0;$i<4;$i++){
                EmployeeWork::create([
                    'user_id' => $request->user_id,
                    'image_url' => null,
                ]);
            }
        }

        // store copy of image in public
        Artisan::call('storage:link');

        return response()->json([
            'status' => true,
            'message' => 'Add Detailes to Profile successfully',
            'employee' => $employee,
            // 'user' => $employee->user,
            // 'works' => $employee->user->works,
        ], 200);
    }



    /**
     * @OA\Post(
     *     path="/api/employee/updateEmployeeCompleteData/{id}",
     *     summary="Update details of an employee",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee to update data",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="desc",
     *                     type="string",
     *                     description="Description of the employee"
     *                 ),
     *                 @OA\Property(
     *                     property="location",
     *                     type="string",
     *                     description="location in details"
     *                 ),
     *                 @OA\Property(
     *                     property="imageSSN",
     *                     type="string",
     *                     format="binary",
     *                     description="Image of the SSN"
     *                 ),
     *                 @OA\Property(
     *                     property="livePhoto",
     *                     type="string",
     *                     format="binary",
     *                     description="Live photo of the employee"
     *                 ),
     *                 @OA\Property(
     *                     property="nationalId",
     *                     type="string",
     *                     description="National ID of the employee"
     *                 ),
     *                 @OA\Property(
     *                     property="min_price",
     *                     type="integer",
     *                     description="Minimum price of the employee"
     *                 ),
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="integer",
     *                     description="User ID of the employee"
     *                 ),
     *                 @OA\Property(
     *                     property="service_id",
     *                     type="integer",
     *                     description="Service ID of the employee"
     *                 ),
     *                 @OA\Property(
     *                     property="works[0][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 1",
     *                      nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="works[1][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 2",
     *                      nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="works[2][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 3",
     *                      nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="works[3][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 4",
     *                      nullable=true,
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee details updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Employee profile details updated successfully"
     *             ),
     *             @OA\Property(
     *                 property="employee",
     *                 type="object"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="object"
     *             )
     *         )
     *     )
     * )
     */



     public function updateEmployeeCompleteData(Request $request, $employeeId)
     {
         $validatedData = Validator::make($request->all(), [
             'desc' => 'required|string',
             'location' => 'required|string',
             'imageSSN' => 'file|mimes:jpeg,png,jpg,gif',
             'livePhoto' => 'file|mimes:jpeg,png,jpg,gif',
             'nationalId' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:13',
             'min_price' => 'required',
             'user_id' => 'required|exists:users,id',
             'service_id' => 'required|exists:services,id',
             'works' => 'nullable|array|max:4',
             'works.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
         ]);
     
         if ($validatedData->fails()) {
             return response()->json(['status' => false, 'message' => $validatedData->errors()], 401);
         }
     
         $employee = Employee::where('id', $employeeId)
         ->where('checkByAdmin', 'rejected')
         ->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'can‘t update data',
            ], 404);
        }
     
         // Update existing fields
         $employee->desc = $request->desc;
         $employee->location = $request->location;
         $employee->nationalId = $request->nationalId;
         $employee->min_price = $request->min_price;
         $employee->user_id = $request->user_id;
         $employee->service_id = $request->service_id;
         $employee->checkByAdmin = 'waiting';
     
         // Handle imageSSN update
         if ($request->hasFile('imageSSN')) {
             // Delete old image if exists
             if ($employee->imageSSN) {
                 Storage::delete(str_replace('/storage', 'public', $employee->imageSSN));
             }
     
             $newImageSsn = $request->file('imageSSN')->store('employees_ssn', 'public');
             $employee->imageSSN = Storage::url($newImageSsn);
         }
     
         // Handle livePhoto update
         if ($request->hasFile('livePhoto')) {
             // Delete old image if exists
             if ($employee->livePhoto) {
                 Storage::delete(str_replace('/storage', 'public', $employee->livePhoto));
             }
     
             $newImageLive = $request->file('livePhoto')->store('employees_live_photo', 'public');
             $employee->livePhoto = Storage::url($newImageLive);
         }
     
         // Save updated employee data
         $employee->save();
     
         // Update works if provided
         if ($request->has('works')) {
             $works = $request->works;
     
             foreach ($works as $index => $work) {
                 $workImageUrl = null;
     
                 if (isset($work['image']) && $work['image']) {
                     // Delete old image if exists
                     $existingWork = EmployeeWork::where('user_id', $employee->user_id)
                         ->orderBy('id', 'asc')
                         ->skip($index)
                         ->first();
     
                     if ($existingWork && $existingWork->image_url) {
                         Storage::delete(str_replace('/storage', 'public', $existingWork->image_url));
                     }
     
                     $workImage = $work['image'];
                     $workImagePath = $workImage->store('employee_works', 'public');
                     $workImageUrl = Storage::url($workImagePath);
                 }
     
                 // Find existing or create new EmployeeWork
                 $employeeWork = EmployeeWork::where('user_id', $employee->user_id)
                     ->orderBy('id', 'asc')
                     ->skip($index)
                     ->first();
     
                 if ($employeeWork) {
                     // Update existing EmployeeWork
                     $employeeWork->image_url = $workImageUrl;
                     $employeeWork->save();
                 } else {
                     // Create new EmployeeWork
                     EmployeeWork::create([
                         'user_id' => $employee->user_id,
                         'image_url' => $workImageUrl,
                     ]);
                 }
             }
         }
     
         return response()->json([
             'status' => true,
             'message' => 'Employee profile details updated successfully',
             'employee' => $employee,
         ], 200);
     }


    /**
     * @OA\Post(
     *     path="/api/employee/updateWorksImage/{user_id}",
     *     summary="Edit an employee works image",
     *     tags={"Employee"},
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
     *                     property="works[0][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 1",
     *                     nullable=true
     *                 ),
     *                 @OA\Property(
     *                     property="works[1][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 2",
     *                     nullable=true
     *                 ),
     *                 @OA\Property(
     *                     property="works[2][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 3",
     *                     nullable=true
     *                 ),
     *                 @OA\Property(
     *                     property="works[3][image]",
     *                     type="string",
     *                     format="binary",
     *                     description="Image work 4",
     *                     nullable=true
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Employee updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Employee updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation errors"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\AdditionalProperties(type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Employee not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Employee not found")
     *         )
     *     )
     * )
     */


     public function updateWorksImage(Request $request, $user_id)
     {
         $validatedData = Validator::make($request->all(), [
             'works' => 'nullable|array|max:4',
             'works.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
         ]);
     
         if ($validatedData->fails()) {
             return response()->json(['status' => false, 'message' => $validatedData->errors()], 401);
         }
     
         if ($request->has('works')) {
             $works = $request->works;
             $works = array_pad($works, 4, ['image' => null]);
             $existingWorks = EmployeeWork::where('user_id', $user_id)->get();
     
             // Check if user exists
             if ($existingWorks->isEmpty()) {
                 return response()->json(['status' => false, 'message' => 'User not found'], 404);
             }
     
             $updateData = [];
             $newWorks = [];
     
             foreach ($works as $index => $work) {
                 $workImageUrl = null;
     
                 if (isset($work['image']) && $work['image']) {
                     $workImage = $work['image'];
                     $workImagePath = $workImage->store('employee_works', 'public');
                     $workImageUrl = Storage::url($workImagePath);
                 }
     
                 if (isset($existingWorks[$index])) {
                     $existingWork = $existingWorks[$index];
                     if ($existingWork->image_url && $workImageUrl) {
                         $oldImagePath = str_replace('/storage', 'public', parse_url($existingWork->image_url, PHP_URL_PATH));
                         Storage::delete($oldImagePath);
                     }
                     $updateData[] = [
                         'id' => $existingWork->id,
                         'image_url' => $workImageUrl ?? $existingWork->image_url,
                     ];
                 } else {
                     $newWorks[] = [
                         'user_id' => $user_id,
                         'image_url' => $workImageUrl,
                     ];
                 }
             }
     
             // Perform bulk update
             if (!empty($updateData)) {
                 foreach ($updateData as $data) {
                     EmployeeWork::where('id', $data['id'])->update([
                         'image_url' => $data['image_url']
                     ]);
                 }
             }
     
             // Perform bulk insert
             if (!empty($newWorks)) {
                 EmployeeWork::insert($newWorks);
             }
     
             // Delete excess works
             if (count($existingWorks) > count($works)) {
                 $excessWorks = $existingWorks->slice(count($works));
                 foreach ($excessWorks as $work) {
                     if ($work->image_url) {
                         $workImagePath = str_replace('/storage', 'public', parse_url($work->image_url, PHP_URL_PATH));
                         Storage::delete($workImagePath);
                     }
                     $work->delete();
                 }
             }
     
             Artisan::call('storage:link');
         }
     
         return response()->json(['status' => true, 'message' => 'Edit employee works image successfully'], 200);
     }


    //  public function updateWorksImage(Request $request, $user_id)
    //  {
    //      $validatedData = Validator::make($request->all(), [
    //          'works' => 'nullable|array|max:4',
    //          'works.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
    //         //  'user_id' => 'required|exists:users,id',
    //      ]);

    //      if ($validatedData->fails()) {
    //          return response()->json(['status' => false, 'message' => $validatedData->errors()], 401);
    //      }

    //      if ($request->has('works')) {
    //          $works = $request->works;

    //          // Ensure there are exactly 4 entries, filling with null values if necessary
    //          $works = array_pad($works, 4, ['image' => null]);

    //          // Retrieve existing works for the user
    //          $existingWorks = EmployeeWork::where('user_id', $user_id)->get();

    //          foreach ($works as $index => $work) {
    //              $workImageUrl = null;

    //              if (isset($work['image']) && $work['image']) {
    //                  $workImage = $work['image'];
    //                  $workImagePath = $workImage->store('employee_works', 'public');
    //                  $workImageUrl = Storage::url($workImagePath);
    //              }

    //              if (isset($existingWorks[$index])) {
    //                  // Update existing work
    //                  $existingWork = $existingWorks[$index];

    //                  // Delete the old image if a new one is uploaded
    //                  if ($existingWork->image_url && $workImageUrl) {
    //                      $oldImagePath = str_replace('/storage', 'public', parse_url($existingWork->image_url, PHP_URL_PATH));
    //                      Storage::delete($oldImagePath);
    //                  }

    //                  $existingWork->update([
    //                      'image_url' => $workImageUrl ?? $existingWork->image_url,
    //                  ]);
    //              } else {
    //                  // Create new work
    //                  EmployeeWork::create([
    //                      'user_id' => $user_id,
    //                      'image_url' => $workImageUrl,
    //                  ]);
    //              }
    //          }

    //          // If there are more existing works than provided, delete the excess and their images
    //          for ($i = count($works); $i < count($existingWorks); $i++) {
    //              $work = $existingWorks[$i];
    //              if ($work->image_url) {
    //                  // Extract the path from the URL
    //                  $workImagePath = str_replace('/storage', 'public', parse_url($work->image_url, PHP_URL_PATH));
    //                  Storage::delete($workImagePath);
    //              }
    //              $work->delete();
    //          }

    //          // Run the storage link command once after processing all works
    //          Artisan::call('storage:link');
    //      }

    //      return response()->json(['status' => true, 'message' => 'Edit employee works image successfully'], 200);
    //  }


    /**
     * @OA\Get(
     *     path="/api/employee/employeeProfile/{id}",
     *     summary="Show employee profile",
     *     description="Show employee profile by employee id",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the employee to show profile",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Show employee profile successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Employee not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */

    public function employeeProfile($id){

        $employee = Employee::find($id);
        $employeeName = $employee->user->name;
        $employeeImage = $employee->user->image;
        $employeePhone = $employee->user->phone;

        // Get the average rating of the employee
        $averageRating = $this->feedbackService->getAverageRatingPerEmployee($id);

        return response()->json([
            'status' => true,
            'message' => 'Employee profile updated successfully',
            'data' => [
                'name' => $employeeName,
                'image' => $employeeImage,
                'desc' => $employee->desc,
                'min_price' => $employee->min_price,
                'status' => $employee->status,
                'phone' => $employeePhone,
                'average_rating' => $averageRating['average_rating'], // Include average rating 
                'total_rates' => $employee->feedbacks->count(),      
            ],
        ], 200);

    }



    /**
     * @OA\Post(
     *     path="/api/employee/editEmployeeProfile/{id}",
     *     summary="Update employee profile",
     *     operationId="editEmployeeProfile",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the employee to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="Name of the employee"
     *                 ),
     *                 @OA\Property(
     *                     property="desc",
     *                     type="string",
     *                     description="Description of the employee"
     *                 ),
     *                 @OA\Property(
     *                     property="min_price",
     *                     type="integer",
     *                     description="Minimum price"
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="Profile image of the employee"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Employee profile updated successfully"
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Validation error message"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Employee not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Employee not found"
     *             )
     *         )
     *     )
     * )
     */


    public function editEmployeeProfile(Request $request, $id)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'desc' => 'sometimes|string',
            'min_price' => 'sometimes|numeric',
        ]);

        if ($validatedData->fails()) {
            return response()->json(['status' => false, 'message' => $validatedData->errors()], 400);
        }

        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['status' => false, 'message' => 'Employee not found'], 404);
        }

        // Update user data
        $user = $employee->user;

        $user->name = $request->has('name') ? $request->name : $user->name;

        if ($request->hasFile('image')) {
            // Delete the old image if exists
            if ($user->image) {
                $oldImagePath = str_replace('/storage', 'public', parse_url($user->image, PHP_URL_PATH));
                Storage::delete($oldImagePath);
            }

            // Store the new image
            $imagePath = $request->image->store('users_folder', 'public');
            $user->image = Storage::url($imagePath);
        }
        $user->save();

        // Update employee data
        $employee->desc = $request->has('desc') ? $request->desc : $employee->desc;
        $employee->min_price = $request->has('min_price') ? $request->min_price : $employee->min_price;
        $employee->save();

        return response()->json([
            'status' => true,
            'message' => 'Employee profile updated successfully',
            'data' => [
                'name' => $user->name,
                'image' => $user->image,
                'desc' => $employee->desc,
                'min_price' => $employee->min_price,
                'status' => $employee->status,
                'phone' => $user->phone,
            ],
        ], 200);
    }



    /**
     * @OA\Post(
     *     path="/api/employee/changeEmployeeStatus/{id}",
     *     summary="Change employee status between ['available', 'busy']",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee",
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
     *                     property="status",
     *                     type="string",
     *                     description="Employee status",
     *                     example="available"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Change employee status successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="true"),
     *             @OA\Property(property="message", type="string", example="Change status successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="No employee found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="false"),
     *             @OA\Property(property="message", type="string", example="No employee found")
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="false"),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\AdditionalProperties(type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="false"),
     *             @OA\Property(property="message", type="string", example="Server error")
     *         )
     *     )
     * )
     */


    // function to change employee status

    public function changeEmployeeStatus(Request $request,$id){
        try{
        $employee=Employee::find($id);
            if($employee){
                $employee->update(['status'=> $request->status]);

                return response()->json([
                    'status' => 'true',
                    'message' => 'change status successfully',
                ],200);
            }

            else {
                return response()->json([
                    'status' => 'false',
                    'message' => 'no employee found',
                ],401);
            }

        }

        catch (Throwable $e) {
            throw $e;
        }
    }



    /**
     * @OA\Post(
     *     path="/api/employee/changeCheckByAdmin/{id}",
     *     summary="Change employee status between ['accepted','rejected']",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee",
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
     *                     property="checkByAdmin",
     *                     type="string",
     *                     description="checkByAdmin to show Employee data",
     *                     example="accepted"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Change employee status successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="true"),
     *             @OA\Property(property="message", type="string", example="Change status successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="No employee found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="false"),
     *             @OA\Property(property="message", type="string", example="No employee found")
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="false"),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\AdditionalProperties(type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="false"),
     *             @OA\Property(property="message", type="string", example="Server error")
     *         )
     *     )
     * )
     */


        // function to change employee status

        public function changeCheckByAdmin(Request $request,$id){
            try{
            $employee=Employee::find($id);
                if($employee){
                    $employee->update(['checkByAdmin'=> $request->checkByAdmin]);
    
                    return response()->json([
                        'status' => 'true',
                        'message' => 'change checkByAdmin successfully',
                    ],200);
                }
    
                else {
                    return response()->json([
                        'status' => 'false',
                        'message' => 'no employee found',
                    ],401);
                }
    
            }
    
            catch (Throwable $e) {
                throw $e;
            }
        }




    /**
     * @OA\Get(
     *     path="/api/employee/showEmployeeLastWorks/{user_id}",
     *     summary="Show all employee work images",
     *     description="Show all employee work images by user ID",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         description="ID of the user to show all last works images",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Show last work successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="true"),
     *             @OA\Property(property="Employee Work Image", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No user found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No user found")
     *         )
     *     )
     * )
     */

    public function showEmployeeLastWorks($id){
        $employeeWork = EmployeeWork::where('user_id',$id)->get();
        if($employeeWork ->count() != 0){
            foreach($employeeWork as $employeeWorks){
                $employeeWorks;
            }
            return response()->json([
                'status' => 'true',
                'Employee Work Image' => $employeeWork,
            ],200);
        }

        else{
            return response()->json([
                'status' => false,
                'message' => 'no user found',
            ],401);
        }

    }





    /**
     * @OA\Get(
     *     path="/api/employee/showAllEmployeesByServiceId/{service_id}",
     *     summary="Get all employees in each service",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="service_id",
     *         in="path",
     *         required=true,
     *         description="ID of the service",
     *         @OA\Schema(
     *             type="integer",
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="allemployee", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No employees found in this service",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No employee found in this service")
     *         )
     *     )
     * )
     */

     public function showAllEmployeesByServiceId($service_id)
     {
         $allEmployees = Employee::where('service_id', $service_id)
         ->where('checkByAdmin','accepted')
         ->get();
     
         if ($allEmployees->count() != 0) {
             $employeesWithRatings = $allEmployees->map(function ($employee) {
                 $employee->user->works;
                 $employee->service;
                 $averageRating = $this->feedbackService->getAverageRatingPerEmployee($employee->id);
                 $totalRates = $employee->feedbacks->count();
     
                 // Add average rating and total rates to the employee
                 $employee->average_rating = $averageRating['average_rating'];
                 $employee->total_rates = $totalRates;
     
                 // Remove the feedbacks relation to avoid including it in the response
                 unset($employee->feedbacks);
     
                 return $employee;
             });
     
             return response()->json([
                 'status' => true,
                 'allemployee' => $employeesWithRatings,
             ], 200);
         }
     
         return response()->json([
             'status' => false,
             'message' => 'No employees found for this service.',
         ], 404);
     }



    /**
     * @OA\Post(
     *     path="/api/employee/search",
     *     summary="Add details to employee",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="query",
     *                     type="string",
     *                     description="what is the word you want to search by it"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="search retrieve response successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="successfully"
     *             ),
     *             @OA\Property(
     *                 property="employee",
     *                 type="object"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="object"
     *             )
     *         )
     *     )
     * )
     */

    //  this function to search in employee model by name in user model and return service relation in employee model with the employee name resonse

    public function searchByName(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json([
                'status' => false,
                'message' => 'No query provided',
            ], 400);
        }

        $searchResult = Employee::whereHas('user', function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%');
        })->get();

        if ($searchResult->count() != 0) {
            // Modify the response format as per your requirements
            return response()->json([
                'status' => true,
                'allemployee' => $searchResult->map(function ($employee) {
                    $averageRating = $this->feedbackService->getAverageRatingPerEmployee($employee->id);
                    $totalRates = $employee->feedbacks->count();
        
                    // Add average rating and total rates to the employee
                    $employee->average_rating = $averageRating['average_rating'];
                    $employee->total_rates = $totalRates;
        
                    // Remove the feedbacks relation to avoid including it in the response
                    unset($employee->feedbacks);
                    return [
                        'employee' => [
                            'id' => $employee->id,
                            'desc' => $employee->desc,
                            'min_price' => $employee->min_price,
                            'status' => $employee->status,
                            'average_rating' => $averageRating['average_rating'],
                            'total_rates' => $totalRates,
                        ],
                        'user' => [
                            'id' => $employee->user->id,
                            'name' => $employee->user->name,
                            'email' => $employee->user->email,
                            'phone' =>$employee->user->phone,
                            'image' =>$employee->user->image ,
                            'works' => $employee->user->works, // Assuming 'works' is a method in User model
                        ],
                        'service' => [
                            'id' => $employee->service->id,
                            'name' => $employee->service->name,
                        ]
                    ];
                }),
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No Result found',
            ], 401);
        }
    }






    /**
     * @OA\Get(
     *     path="/api/employee/getTotalOrders/{id}/orders/total",
     *     summary="Get total count of orders for employee",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee",
     *         @OA\Schema(
     *             type="integer",
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="total orders", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No employee found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No employee found")
     *         )
     *     )
     * )
     */

     public function getTotalOrders($employeeId)
     {
         $employee = Employee::find($employeeId);
 
         if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'no employee found',
            ],404);
         }
 
         $totalOrders = $employee->orders()->count();
 
         return response()->json([
            'status' => true,
            'total orders' => $totalOrders,
        ],200);
     }


    /**
     * @OA\Get(
     *     path="/api/employee/notifications/{id}",
     *     summary="Show all notifications for employee by ID",
     *     tags={"Employee"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee",
     *         @OA\Schema(
     *             type="integer",
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Employee not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Employee not found")
     *         )
     *     )
     * )
     */

    // return user notifications

    public function notifications($id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }
        

        $notifications = $employee->notifications->map(function ($notification) {
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



}
