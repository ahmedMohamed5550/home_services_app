<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Services\FeedbackService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Throw_;
use Throwable;

class FeedbackController extends Controller
{

    protected $feedbackService;

    public function __construct(FeedbackService $feedbackService)
    {
        $this->feedbackService = $feedbackService;
    }

    public function getAverageRatingPerEmployee($employeeId)
    {
        $averageRating = $this->feedbackService->getAverageRatingPerEmployee($employeeId);
        return response()->json($averageRating, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/makeFeedback",
     *     summary="Add new feedback to employee",
     *     tags={"Feedback"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="comment",
     *                      type="string",
     *                     description="Feedback comment",
     *                     nullable=true
     *                 ),
     *                 @OA\Property(
     *                     property="rating",
     *                     type="integer",
     *                     description="Rating (1-5)"
     *                 ),
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="integer",
     *                     description="User ID"
     *                 ),
     *                 @OA\Property(
     *                     property="employee_id",
     *                     type="integer",
     *                     description="Employee ID"
     *                 ),
     *                 required={"rating", "user_id", "employee_id"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response="201", description="Feedback added successfully"),
     *     @OA\Response(response="401", description="Validation errors", @OA\JsonContent())
     * )
     */
    public function makeFeedback(Request $request)
    {

        // validation to input
        $validator = validator::make($request->all(), [
            'comment' => 'nullable',
            'rating' => 'required|in:1,2,3,4,5',
            'user_id' => 'required|integer|exists:users,id',
            'employee_id' => 'required|integer|exists:employees,id',
        ]);

        // return message failed if validation is false
        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'message' => $validator->errors()
            ], 401);
        }

        $feedback = Feedback::create([
            'comment' => $request->comment ?? "none",
            'rating' => $request->rating,
            'user_id' => $request->user_id, // auth by user id
            'employee_id' => $request->employee_id,
        ]);

        if ($feedback) {
            return response()->json([
                'status' => 'true',
                'message' => 'make feedback done',
            ], 200);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/getEmployeeFeedback/{employee_id}",
     *     summary="Get all feedback for an employee",
     *     tags={"Feedback"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="employee_id",
     *         in="path",
     *         required=true,
     *         description="Employee ID",
     *         @OA\Schema(
     *             type="integer",
     *         ),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="No feedback found for this employee",
     *         @OA\JsonContent()
     *     ),
     * )
     */

    //  function to get all feedback by employee_id and retrieve all data

    public function getEmployeeFeedback($id)
    {
        try {
            $feedbacks = Feedback::where('employee_id', $id)
                ->with('user')
                ->get();

            if ($feedbacks->isNotEmpty()) {
                return response()->json([
                    'status' => 'true',
                    'message' => $feedbacks,
                ], 200);
            } else {
                return response()->json([
                    'status' => 'false',
                    'message' => 'No feedback found for this employee',
                ], 404);
            }
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while retrieving feedback',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/deleteFeedback/{id}",
     *     summary="Delete a feedback",
     *     tags={"Feedback"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the feedback to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Feedback deleted successfully",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No feedback found",
     *         @OA\JsonContent()
     *     )
     * )
     */

    public function deleteFeedback($id)
    {
        $feedback = Feedback::find($id);
        if ($feedback) {
            $feedback->delete();
            return response()->json([
                'status' => 'true',
                'message' => 'feedback deleted succesfully',
            ], 200);
        } else {
            return response()->json([
                'status' => 'false',
                'message' => 'no feedback found',
            ], 401);
        }
    }


    // /**
    //  * @OA\Get(
    //  *     path="/api/getAverageRatingPerEmployee/{employee_id}",
    //  *     summary="Show average rating for employee by ID",
    //  *     tags={"Feedback"},
    //  *     security={{"bearerAuth":{}}},
    //  *     @OA\Parameter(
    //  *         name="employee_id",
    //  *         in="path",
    //  *         required=true,
    //  *         description="ID of the employee to show average rating to it",
    //  *         @OA\Schema(
    //  *             type="integer",
    //  *         ),
    //  *     ),
    //  *     @OA\Response(
    //  *         response=200,
    //  *         description="Success",
    //  *         @OA\JsonContent(
    //  *             type="object",
    //  *             @OA\Property(property="status", type="boolean", example=true),
    //  *             @OA\Property(property="message", type="array", @OA\Items(type="object"))
    //  *         )
    //  *     ),
    //  *     @OA\Response(
    //  *         response=404,
    //  *         description="Employee not found",
    //  *         @OA\JsonContent(
    //  *             type="object",
    //  *             @OA\Property(property="status", type="boolean", example=false),
    //  *             @OA\Property(property="message", type="string", example="Employee not found")
    //  *         )
    //  *     )
    //  * )
    //  */


    //  public function getAverageRatingPerEmployee($employeeId)
    //  {
    //      // Retrieve rating counts for the specific employee
    //      $averageRating = Feedback::where('employee_id', $employeeId)
    //          ->select(DB::raw('SUM(rating) / COUNT(*) AS average_rating'))
    //          ->groupBy('employee_id')
    //          ->first(); // Use first() instead of get() to get a single record
 
    //      // Extract the average rating value
    //      $averageRatingValue = $averageRating ? $averageRating->average_rating : null;
 
    //      return response()->json(['average_rating' => $averageRatingValue],200);
    //  }



    // /**
    //  * @OA\Get(
    //  *     path="/api/getAverageRatingPerEmployee",
    //  *     summary="Get average rating per employee",
    //  *     tags={"Feedback"},
    //  *     security={{"bearerAuth":{}}},
    //  *     @OA\Response(
    //  *         response=200,
    //  *         description="Success",
    //  *         @OA\JsonContent(
    //  *             type="array",
    //  *             @OA\Items(
    //  *                 @OA\Property(property="employee_id", type="integer"),
    //  *                 @OA\Property(property="average_rating", type="number", format="float"),
    //  *             )
    //  *         )
    //  *     ),
    //  *     @OA\Response(
    //  *         response=401,
    //  *         description="Unauthorized",
    //  *         @OA\JsonContent()
    //  *     ),
    //  * )
    //  */


    // public function  getAverageRatingPerEmployee()
    // {

    //     // Retrieve rating counts for each employee
    //     $averageRatings = Feedback::select('employee_id', DB::raw('SUM(rating) / COUNT(*) AS average_rating'))
    //         ->groupBy('employee_id')
    //         ->get();

    //     return response()->json($averageRatings);
    // }



    /**
     * @OA\Post(
     *     path="/api/editFeedback/{id}",
     *     summary="Edit a feedback",
     *     operationId="editFeedback",
     *     tags={"Feedback"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the feedback",
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
     *                     property="comment",
     *                     type="string",
     *                     description="Comment of the feedback"
     *                 ),
     *                 @OA\Property(
     *                     property="rating",
     *                     type="integer",
     *                     description="Rating (1-5)"
     *                 ),
     *                 required={"comment", "rating"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Feedback updated successfully",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Validation errors",
     *         @OA\JsonContent()
     *     ),
     * )
     */




    public function editFeedback(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'comment' => 'string',
                'rating' => 'in:1,2,3,4,5',
            ]);

            // Return message failed if validation fails
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'false',
                    'message' => $validator->errors()
                ], 401);
            }

            // Find the feedback by ID
            $feedback = Feedback::find($id);

            // Check if feedback exists
            if (!$feedback) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Feedback not found'
                ], 404);
            }

            // Update comment if provided in request
            if ($request->has('comment')) {
                $feedback->comment = $request->input('comment');
            }

            // Update rating if provided in request
            if ($request->has('rating')) {
                $feedback->rating = $request->input('rating');
            }

            // Save the changes to the feedback
            $feedback->save();

            // Return success response
            return response()->json([
                'status' => 'true',
                'message' => 'Feedback updated successfully',
                'feedback' => $feedback // Optionally return the updated feedback
            ], 200);
        } catch (Throwable $e) {
            // Handle any unexpected errors
            throw $e;
        }
    }
}
