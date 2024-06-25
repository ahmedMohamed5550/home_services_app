<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class orderController extends Controller
{
   public function showAllOrder()
   {
    
    $orders = Order::with(['user', 'employee.user', 'employee.service'])->paginate();

    return view("Orders.all", compact('orders'));

        
       
    }
}
