<?php 
$is_Accepted=0;
$is_Rejected=0;
$is_Wating=0;
?>


@extends('layout')
@section('body')
@include('succsess')
<table class="table">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">customer Name</th>
        <th scope="col">Employee Name</th>
        <th scope="col">services Name</th>
        <th scope="col">Price</th>
        <th scope="col">Price_after_disc</th>
        <th scope="col">status</th>
        <th scope="col">location</th>
        <th scope="col">order desc</th>
        <th scope="col">data of delivery</th>
        <th scope="col">voucher_code</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order )
      <tr>
          <th scope="row">{{$loop->iteration}}</th>
          <td>{{ $order->user->name }}</td>
          <td>{{  $order->employee->user->name }}</td> 
          <td>{{   $order->employee->service->name }}</td> 
          <td>{{   $order->price }}</td> 
          <td>{{   $order->price_after_discount }}</td>
          @if($order->status=="waiting")
          <?php 
          $is_Wating++
          ?>
 
          <td class="text-warning">{{   $order->status }}</td> 
          @elseif ($order->status=="rejected")
          <?php 
          $is_Rejected++
          ?>
          <td class="text-danger">{{   $order->status }}</td> 
          @else
          <?php 
          $is_Accepted++
          ?>
          <td class="text-success">{{   $order->status }}</td> 

          @endif 
          
          <td>{{   $order->location }}</td> 
          <td>{{   $order->order_descriptions }}</td> 
          <td>{{   $order->date_of_delivery }}</td> 
          <td>{{   $order->voucher_code }}</td> 
       
    </tr>
    @endforeach

    </tbody>
  </table>

  <br> <br>

  <div class="row">
    <div class="col-sm-4 grid-margin">
      <div class="card">
        <div class="card-body">
          <h5 class="text-success"> Completed</h5>
          <div class="row">
            <div class="col-8 col-sm-12 col-xl-8 my-auto">
              <div class="d-flex d-sm-block d-md-flex align-items-center">
                <h2 class="mb-0 text-success">{{ $is_Accepted }}</h2>
              </div>
              <h6 class="text-muted font-weight-normal">this is num of orders are completed</h6>
              
            </div>
            <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
             
              <i class="icon-lg mdi mdi-check-circle text-success ms-auto"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-4 grid-margin">
      <div class="card">
        <div class="card-body">
          <h5 class="text-warning"> Waiting:</h5>
          <div class="row">
            <div class="col-8 col-sm-12 col-xl-8 my-auto">
              <div class="d-flex d-sm-block d-md-flex align-items-center">
                <h2 class="mb-0 text-warning">{{ $is_Wating }}</h2>
              </div>
              <h6 class="text-muted font-weight-normal">this is num of orders are waiting</h6>
              
            </div>
            <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
              <i class="icon-lg mdi mdi-clock-outline text-warning ms-auto"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-4 grid-margin">
      <div class="card">
        <div class="card-body">
          <h5 class="text-danger"> Rejected:</h5>
          <div class="row">
            <div class="col-8 col-sm-12 col-xl-8 my-auto">
              <div class="d-flex d-sm-block d-md-flex align-items-center">
                <h2 class="mb-0 text-danger">{{ $is_Rejected }}</h2>
              </div>
              <h6 class="text-muted font-weight-normal">this is num of orders are Rejected</h6>
              
            </div>
            <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
              <i class="icon-lg mdi mdi-close-circle-outline text-danger ms-auto"></i>

            </div>
          </div>
        </div>
      </div>
    </div>
   
  </div>
  
  


@endsection