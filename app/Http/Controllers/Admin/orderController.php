<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class orderController extends Controller
{
   public function showAllOrder()
   {
    
    $orders = Order::with(['user', 'employee.user', 'employee.service'])->paginate();
    $ordersByService = Order::join('employees', 'orders.employee_id', '=', 'employees.id')
    ->join('services', 'employees.service_id', '=', 'services.id')
    ->select('services.name', DB::raw('count(orders.id) as total_orders'))
    ->groupBy('services.name')
    ->get();

    return view("Orders.all", compact('orders', 'ordersByService'));
    }


 
    // public function showOrder($id)
    // {
    // // Fetch the order by ID, including related user, employee, and service
    // $order = Order::with(['user', 'employee.user', 'employee.service'])->findOrFail($id);

    // return view("Orders.show", compact('order'));
    // }
}
