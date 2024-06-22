@extends('layout')
@section('body')
@include('errors')
@include('succsess')
<form method="POST" action="{{ url("admin/services/edit/$service->id") }}" enctype="multipart/form-data">
    @csrf
    @method('put')
    <div class="form-group">
      <label for="exampleInputEmail1">service Name</label>
      <input type="text" name="name" class="form-control text-white" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter name" value="{{ $service->name }}" >

    <div class="form-group">
        <label for="exampleInputEmail1">product desc</label>
        <textarea type="text" name="desc" class="form-control text-white" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter desc">{{ $service->desc }}</textarea>
      </div>

      <div class="form-group">
        <input type="file" name="image" class="form-control text-white" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter image" value="{{ $service->image }}" >

      </div>


    <button type="submit" class="btn btn-primary">Submit</button>
</form>

@endsection