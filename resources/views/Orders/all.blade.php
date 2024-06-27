<?php 
$is_Accepted = 0;
$is_Rejected = 0;
$is_Waiting = 0;
?>

@extends('layout')
@section('body')

  <input id="myInput" class="form-control w-50 text-white" type="text" name="search" placeholder="Search orders">
  @include('succsess')

  <table id="myTable" class="table">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">Customer Name</th>
        <th scope="col">Employee Name</th>
        <th scope="col">Services Name</th>
        <th scope="col">Price</th>
        <th scope="col">Price after Discount</th>
        <th scope="col">Status</th>
        <th scope="col">Location</th>
        <th scope="col">Order Description</th>
        <th scope="col">Date of Delivery</th>
        <th scope="col">Voucher Code</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order)
          <tr>
            <th scope="row">{{ $loop->iteration }}</th>
            <td>{{ $order->user->name }}</td>
            <td>{{ $order->employee->user->name }}</td>
            <td>{{ $order->employee->service->name }}</td>
            <td>{{ $order->price }}</td>
            <td>{{ $order->price_after_discount }}</td>
            @if ($order->status == "waiting")
              @php $is_Waiting++ @endphp
              <td class="text-warning">{{ $order->status }}</td>
            @elseif ($order->status == "rejected")
              @php $is_Rejected++ @endphp
              <td class="text-danger">{{ $order->status }}</td>
            @else
              @php $is_Accepted++ @endphp
              <td class="text-success">{{ $order->status }}</td>
            @endif
            <td>{{ $order->location }}</td>
            <td>{{ $order->order_descriptions }}</td>
            <td>{{ $order->date_of_delivery }}</td>
            <td>{{ $order->voucher_code }}</td>
          </tr>
        @endforeach
    </tbody>
  </table>

  <br><br>

  <div class="row">
    <div class="col-sm-4 grid-margin">
      <div class="card">
        <div class="card-body">
          <h5 class="text-success">Completed</h5>
          <div class="row">
            <div class="col-8 col-sm-12 col-xl-8 my-auto">
              <div class="d-flex d-sm-block d-md-flex align-items-center">
                <h2 class="mb-0 text-success">{{ $is_Accepted }}</h2>
              </div>
              <h6 class="text-muted font-weight-normal">This is the number of completed orders</h6>
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
          <h5 class="text-warning">Waiting</h5>
          <div class="row">
            <div class="col-8 col-sm-12 col-xl-8 my-auto">
              <div class="d-flex d-sm-block d-md-flex align-items-center">
                <h2 class="mb-0 text-warning">{{ $is_Waiting }}</h2>
              </div>
              <h6 class="text-muted font-weight-normal">This is the number of waiting orders</h6>
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
          <h5 class="text-danger">Rejected</h5>
          <div class="row">
            <div class="col-8 col-sm-12 col-xl-8 my-auto">
              <div class="d-flex d-sm-block d-md-flex align-items-center">
                <h2 class="mb-0 text-danger">{{ $is_Rejected }}</h2>
              </div>
              <h6 class="text-muted font-weight-normal">This is the number of rejected orders</h6>
            </div>
            <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
              <i class="icon-lg mdi mdi-close-circle-outline text-danger ms-auto"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <hr>

    <h2 class="m-3">Total Orders by Service</h2>

    @foreach ($ordersByService as $service)
      <div class="col-sm-4 grid-margin">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-8 col-sm-12 col-xl-8 my-auto">
                <div class="d-flex d-sm-block d-md-flex align-items-center">
                  <h2 class="mb-0">{{ $service->name }}: {{ $service->total_orders }} orders</h2>
                </div>
              </div>
              <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                <i class="icon-lg mdi mdi-account-clock text-behance ms-auto font-large-2"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>

@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function(){
    $("#myInput").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      $("#myTable tbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
      });
    });
  });
</script>
