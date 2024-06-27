@extends('layout')
@section("body")

@foreach($employee as $emp)
<div class="container w-50 h-25">
    <h3 class="text-center m-2">Employee Details</h3>
    <div class="card ">
        <div class="row no-gutters">
            <div class="col-md-6 mx-auto h-50">
                <img src="{{asset("storage/$emp->imageSSN")}}" class="card-img  h-50  " alt="Image 1">
            </div>
            <div class="col-md-6 mx-auto  h-50">
                <img src="{{asset("storage/$emp->livePhoto")}}" class="card-img  h-50  " alt="Image 2">
            </div>
        </div>
        <div class="card-body">
            <h5 class="card-title">Name : {{ $emp->name }}</h5>
            <hr>
            <p class="card-text">Desc : {{ $emp->desc }}</p>
            <hr>
            <p class="card-text">id : {{ $emp->nationalId  }}</p>
            <hr>
            <p class="card-text">price : {{ $emp->min_price  }}$</p>
            <hr>
            <p class="card-text">phone : {{ $emp->phone  }}$</p>
            <hr>
            <a href="{{ url("admin/checkByAdmin/{$emp->id}/accepted") }}" class="badge badge-outline-success">Accepted</a></td>
            <td><a href="{{ url("admin/checkByAdmin/{$emp->id}/rejected") }}" class="badge badge-outline-danger">Rejected</a></td>


        </div>
    </div>
</div>
@endforeach

    
@endsection