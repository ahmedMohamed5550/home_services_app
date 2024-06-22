<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Order;
use App\Models\User;
use App\Models\Voucher;
use App\Notifications\sendNotifyToEmployeeAboutOrder;
use App\Notifications\sendNotifyToUserAboutEmployeeResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\Throw_;
use Throwable;

class orderController extends Controller
{

    /**
     * @OA\Post(
     * path="/api/makeOrder",
     * summary="add new order",
     * tags={"Order"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="price",
     * in="query",
     * description="order price",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Parameter(
     * name="location",
     * in="query",
     * description="order location",
     * required=true,
     * @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     * name="date_of_delivery",
     * in="query",
     * description="date of delivery to order in dateTime",
     * example="2020-12-23 17:40:00",
     * required=true,
     * @OA\Schema(type="string",format="date-time")
     * ),
     * @OA\Parameter(
     * name="order_descriptions",
     * in="query",
     * description="order_descriptions",
     * required=true,
     * @OA\Schema(type="string")
     * ),
    * @OA\Parameter(
     * name="voucher_code",
     * in="query",
     * description="make discount in order price if has a voucher code valid",
     * @OA\Schema(type="string")
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

     * @OA\Response(response="201", description="make order successfully"),
     * @OA\Response(response="422", description="Validation errors")
     * )
     */


    public function makeOrder(Request $request){

    // validation to input
    $validator = Validator::make($request->all(),[
        'price' => 'required',
        'location' =>'required|string',
        'date_of_delivery' => 'required|after_or_equal:' . date('Y-m-d H:i:s'), // 2020-12-23 17:40:00
        'user_id' => 'required|integer|exists:users,id',
        'employee_id' => 'required|integer|exists:employees,id',
        'order_descriptions' => 'required|string',
        'voucher_code' => 'sometimes|exists:vouchers,code'
        ]);

    // return message failed if validation is false
    if($validator->fails()){
        return response()->json([
        'status' => 'false',
        'message' => $validator->errors()
        ],401);
    }

    $order = new Order();
    $order->price = $request->price;
    $order->location = $request->location;
    $order->date_of_delivery = $request->date_of_delivery;
    $order->user_id = $request->user_id;
    $order->employee_id = $request->employee_id;
    $order->order_descriptions = $request->order_descriptions;
    $order->voucher_code = $request->voucher_code;

    if($request->has('voucher_code')){
        $voucher = Voucher::where('code',$request->voucher_code)
        ->where('expired_at','>',today())
        ->where('status','active')
        ->first();

        if($voucher){
            $price_after_discount = $order->price - $voucher->discount;
            $order->voucher_id = $voucher->id;
            $order->price_after_discount = $price_after_discount;
            $total_discount = $order->price - $order->price_after_discount;
            $order->total_discount = $total_discount;
        }
    }

    else{
        $order->voucher_code = null;
    }

    $order->save();

    // who is send to a notification
    $order_employee = User::where('id',$order->employee_id)->get();

    Notification::send($order_employee,new sendNotifyToEmployeeAboutOrder(
        $order->id,
        $order->location,
        $order->date_of_delivery,
        $order->order_descriptions,
        $order->user->name,
    ));

    return response()->json([
        'status' => 'true',
        'message' => 'make order done'
    ],200);


    }


    /**
     * @OA\Get(
     *     path="/api/getUserOrders/{user_id}",
     *     summary="Get all users order",
     *     tags={"Order"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="user id",
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

    //  function to get users order by id and retrieve all data about my order

    public function getUserOrders($id){
        try{

            $orders = Order::where('user_id','=',$id)->get();

            if($orders ->count() != 0){
                foreach($orders as $order){
                $order->user; // return user data who reserved order
                $order->employee->user; // return employee data who reserved with it
                $order->employee->service; // return service who reserved it
                }
                return response()->json([
                    'status' => 'true',
                    'message' => $orders,
                ],200);
            }

            else{
                return response()->json([
                    'status' => 'false',
                    'message' => 'no order found',
                ],401);
            }
        }

        catch (Throwable $e) {
            throw $e;
        }


    }


    /**
     * @OA\Get(
     *     path="/api/getEmployeeOrders/{employee_id}",
     *     summary="Get all employees order",
     *     tags={"Order"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="employee_id",
     *         in="path",
     *         required=true,
     *         description="employee id",
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

    //  function to get users order by id and retrieve all data about my order

    public function getEmployeeOrders($id){
        try{

            $orders = Order::where('employee_id','=',$id)->get();

            if($orders ->count() != 0){
                foreach($orders as $order){
                $order->user; // return user data who reserved order
                $order->employee->user; // return employee data who reserved with it
                $order->employee->service; // return service who reserved it
                }
                return response()->json([
                    'status' => 'true',
                    'message' => $orders,
                ],200);
            }

            else{
                return response()->json([
                    'status' => 'false',
                    'message' => 'no order found',
                ],401);
            }
        }

        catch (Throwable $e) {
            throw $e;
        }


    }



        /**
     * @OA\Post(
     *     path="/api/changeOrderStatus/{order_id}",
     *     summary="change order Status between ['accepted','waiting','rejected']",
     *     operationId="get_order_id",
     *     tags={"Order"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="order_id",
     *         in="path",
     *         required=true,
     *         description="ID of the order",
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
     *                     description="order status"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="change order status successfully"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors"
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */



    // function to change status

    public function changeOrderStatus(Request $request,$id){
        try{
        $order=Order::find($id);
            if($order){
                $order->update(['status'=> $request->status]);

            // who is send to anotifications

            $order_response_to_user = User::where('id',$order->user_id)->get();

            Notification::send($order_response_to_user,new sendNotifyToUserAboutEmployeeResponse(
                $order->id,
                $order->employee->user->name,
                $order->status,
            ));
                return response()->json([
                    'status' => 'true',
                    'message' => 'change status successfully',
                ],200);
            }

            else {
                return response()->json([
                    'status' => 'false',
                    'message' => 'no order found',
                ],401);
            }

        }

        catch (Throwable $e) {
            throw $e;
        }
    }

        /**
     * @OA\Get(
     * path="/api/orders",
     * summary="show all orders to admin",
     * tags={"Order"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(response="200", description="show all order succesfully"),
     * )
     */

    // function show all orders to admins only

    public function index(){

        $orders = Order::paginate();

        if($orders){
            foreach($orders as $order){
            $order->user; // return user data who reserved order
            $order->employee->user; // return employee data who reserved with it
            $order->employee->service; // return service who reserved it
            }
            return response()->json([
                'status' => 'true',
                'message' => $orders,
            ],200);
        }

        else{
            return response()->json([
                'status' => 'false',
                'message' => 'no order found',
            ],401);
        }

    }

    /**
 * @OA\Delete(
 *     path="/api/deleteOrder/{order_id}",
 *     summary="Delete an order",
 *     description="Delete order by ID",
 *     tags={"Order"},
*     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="order_id",
 *         in="path",
 *         description="ID of the order to delete",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="order deleted successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="order not found"
 *     )
 * )
 */

    // function delete order to admins only

    public function deleteOrder($id){
        $order = Order::find($id);
        if($order){
        $order->delete();
        return response()->json([
            'status' => 'true',
            'message' => 'order deleted succesfully',
        ],200);
        }
        else{
            return response()->json([
                'status' => 'false',
                'message' => 'no order found',
            ],401);
        }

    }


}