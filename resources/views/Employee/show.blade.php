@extends('layout')
@section("body")

<style>

</style>

@foreach($employee as $emp)
<div class="container w-50 h-25">
    <h3 class="text-center m-2">Employee Details</h3>
    <div class="card">
        <div class="row no-gutters">
            <div class="col-md-6 d-flex justify-content-center align-items-center p-2">
                {{-- <img src="{{ url('storage/employees_ssn/ITVVkbMDPAlk6fKJDPMljiTAKZscgkdzOnW3iSlX.png') }}" alt="Employee Photo" class="img-fluid" style="max-height: 150px;"> --}}
                <img src="{{url("$emp->imageSSN")}}" class="img-fluid" style="max-height: 200px;" alt="Image 1">
            </div>
            <div class="col-md-6 d-flex justify-content-center align-items-center p-2">
                {{-- <img src="{{ url('storage/employees_live_photo/nTZMPuIPetT1hRxP6Jhs2ozt3LyhG1Kw4isoiC5D.jpg') }}" alt="Employee Photo" class="img-fluid" style="max-height: 150px;"> --}}
                <img src="{{url("$emp->livePhoto")}}" class="img-fluid" style="max-height: 200px;" alt="Image 1">
            </div>
        </div>
        <div class="card-body">
            <h5 class="card-title">Name: {{ $emp->name }}</h5>
            <hr>
            <p class="card-text">Desc: {{ $emp->desc }}</p>
            <hr>
            <p class="card-text">ID: {{ $emp->nationalId }}</p>
            <hr>
            <p class="card-text">Price: {{ $emp->min_price }}$</p>
            <hr>
            <p class="card-text">Phone: {{ $emp->phone }}</p>
            <hr>
            <a href="{{ url("admin/checkByAdmin/{$emp->id}/accepted") }}" class="badge badge-outline-success">Accepted</a>
            <a href="{{ url("admin/checkByAdmin/{$emp->id}/rejected") }}" class="badge badge-outline-danger">Rejected</a>
        </div>
    </div>
</div>

@endforeach

    
@endsection