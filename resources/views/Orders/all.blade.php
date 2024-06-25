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
          <td class="text-danger">{{   $order->status }}</td> 
          @else
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


@endsection