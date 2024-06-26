<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\UserVoucher;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class voucherController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/vouchers",
     *     summary="Show all vouchers",
     *     tags={"Vouchers"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="vouchers", type="array", @OA\Items(type="object"))
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
         $voucher = Voucher::all();
        //  $user_voucher = UserVoucher::all()->count();
         if($voucher->count() != 0){
             return response()->json([
                 'status' => true,
                 'vouchers' => $voucher,
                //  'user_voucher' => $user_voucher,
             ],200);
         }
         else{
             return response()->json([
                 'status' => false,
                 'message' => "No Voucher Found"
             ],401);
         }

     }

    /**
     * @OA\Post(
     * path="/api/addVoucher",
     * summary="add new voucher",
     * tags={"Vouchers"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="code",
     * in="query",
     * description="voucher code",
     * required=true,
     * @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     * name="type",
     * in="query",
     * description="voucher type between [fixed,percent]",
     * required=true,
     * @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     * name="discount",
     * in="query",
     * description="voucher discount",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Parameter(
     * name="status",
     * in="query",
     * description="status to voucher between [inactive,used,expired]",
     * @OA\Schema(type="string")
     * ),
    * @OA\Parameter(
     * name="expired_at",
     * in="query",
     * description="voucher expired at",
     * @OA\Schema(type="string",format="date")
     * ),

     * @OA\Response(response="201", description="make voucher successfully"),
     * @OA\Response(response="422", description="Validation errors")
     * )
     */


    public function addVoucher(Request $request)
    {

        $this->validate($request,[
            'code'=>'string|required',
            'type'=>'required|in:fixed,percent',
            'discount'=>'required|numeric',
            'status'=>'required|in:inactive,used,expired',
            'expired_at' => 'required|date_format:Y-m-d',
        ]);
        $data=$request->all();
        $status=Voucher::create($data);
        if($status){
            return response()->json([
                'status' => 'true',
                'message' => 'make voucher done',
            ],200);
        }
        else{
            return response()->json([
                'status' => 'false',
                'message' => 'failed',
            ],404);
        }
        return 0;
    }

    // public function couponStore(Request $request){

    //     $voucher = Voucher::where('code', $request->code)->first();

    //     if (!$voucher) {
    //         return response()->json(['error' => 'Invalid voucher code, Please try again'], 404);
    //     }

    //     $totalPrice = Order::where('user_id',1)->where('id', null)->sum('price');

    //     $discountValue = $voucher->discount($totalPrice);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'voucher successfully applied',
    //         'coupon' => [
    //             // 'id' => $voucher->id,
    //             'code' => $voucher->code,
    //             'value' => $discountValue,
    //         ],
    //     ]);
    // }
}
