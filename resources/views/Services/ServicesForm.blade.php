@extends('layout')
@section('body')
@include('errors')
@include('succsess')

<form method="POST" action="{{url("admin/services")}}" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
      <label for="exampleInputEmail1">Services Name</label>
      <input type="text" name="name" class="form-control text-white" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter name" value="{{ old('name') }}" >

    <div class="form-group">
        <label for="exampleInputEmail1">Services desc</label>
        <textarea type="text" name="desc" class="form-control text-white" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter desc">{{ old('desc') }}</textarea>
      </div>
    <div class="form-group">
        <input type="file" name="image" class="form-control text-white" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter image" value="{{ old('image') }}" >

      </div>


    <button type="submit" class="btn btn-primary">Submit</button>
</form>

@endsection