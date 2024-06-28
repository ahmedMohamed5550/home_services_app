<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class voucherController extends Controller
{
    public function showVoucherForm()
    {
        return view("Voucher.VoucherForm");
    }



    public function storeVoucher(Request $request)
    {
        $data=$request->validate([

            'code'=>'string|required',
            'type'=>'required|in:fixed,percent',
            'discount'=>'required|numeric',
            'status'=>'required|in:avialable,expired',
            'expired_at' => 'required|date_format:Y-m-d',

          ]);

           Voucher::create($data);
          return redirect(url("admin/addVoucher"))->with('succsess',"inserted is succsessfuly");
        }


        public function getAllVoucher()
        {
            $vouchers=Voucher::all();
            return view("Voucher.all",compact("vouchers"));
        }


        public function showVoucher($id)
        {
            $voucher=Voucher::findOrFail($id);
           return view("Voucher.showOne",compact("voucher"));
        }

        public function showEditForm($id)
        {
            $voucher=Voucher::findOrFail($id);
            return view("Voucher.edit",compact("voucher"));
        }

        public function editVoucher(Request $request , $id)
        {

            $data=$request->validate([

                'code'=>'string|required',
                'type'=>'required|in:fixed,percent',
                'discount'=>'required|numeric',
                'status'=>'required|in:avialable,expired',
                'expired_at' => 'required|date_format:Y-m-d',

              ]);
              $voucher=Voucher::findOrFail($id);
              $voucher->update($data);
              return redirect(url("admin/voucher/show/$voucher->id"))->with('succsess',"updated is succsessfuly");


        }

        public function deleteVoucher($id)
        {
            $voucher=Voucher::findOrFail($id);
            $voucher->delete();
            return redirect(url("admin/Voucher"))->with('succsess',"deleted is succsessfully");

        }



}
