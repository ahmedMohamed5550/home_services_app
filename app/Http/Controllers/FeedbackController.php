<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Throw_;
use Throwable;

class FeedbackController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/makeFeedback",
     * summary="add new feedbackto employee ",
     *  tags={"Feedback"},
     *  security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="comment",
     * in="query",
     * description="order comment",
     * required=true,
     * @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     * name="rating",
     * in="query",
     * description="rating",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Parameter(
     * name="user_id",
     * in="query",
     * description="user id",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Parameter(
     * name="employee_id",
     * in="query",
     * description="employee id",
     * required=true,
     * @OA\Schema(type="integer")
     * ),

     * @OA\Response(response="201", description="make feedback successfully"),
     * @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function makeFeedback(Request $request)
    {

        // validation to input
        $validator = validator::make($request->all(), [
            'comment' => 'required|string',
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
            'comment' => $request->comment,
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
     *     summary="Get all employee feedback",
     *      tags={"Feedback"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="employee_id",
     *         in="path",
     *         required=true,
     *         description="employee_id",
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

    //  function to get all feedback by employee_id and retrieve all data

    public function getEmployeeFeedback($id)
    {
        try {
            $feedbacks = Feedback::where('employee_id', $id)
                ->with('user', 'employee.user', 'employee.service')
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
     *     summary="Delete an feedback",
     *     description="Delete feedback by ID",
     *      tags={"Feedback"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the feedbck to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="feedback deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="feedback not found"
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


    /**
     * @OA\Get(
     *     path="/api/getAverageRatingPerEmployee",
     *     summary="Get getAverageRatingPerEmployee",
     *      tags={"Feedback"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *     ),
     *
     * )
     */
    public function  getAverageRatingPerEmployee()
    {

        // Retrieve rating counts for each employee
        $averageRatings = Feedback::select('employee_id', DB::raw('SUM(rating) / COUNT(*) AS average_rating'))
            ->groupBy('employee_id')
            ->get();

        return response()->json($averageRatings);
    }



    /**
     * @OA\Post(
     *     path="/api/editFeedback/{id}",
     *     summary="Edit an Feedback",
     *     operationId="editFeedback",
     *     tags={"Feedback"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Feedback",
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
     *                     description="comment of Employee"
     *                 ),
     *                 @OA\Property(
     *                     property="rating",
     *                     type="integer",
     *                     description="rating Employee"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Feedback updated successfully"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors"
     *     ),
     *     security={{"bearerAuth": {}}}
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
