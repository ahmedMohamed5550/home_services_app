@extends('layout')
@section('body')
<table class="table">
    <thead>
      <tr>

        <th scope="col">image</th>
        <th scope="col">Name</th>
        <th scope="col">desc</th>
        <th scope="col">Delete</th>
        <th scope="col">Update</th>
      </tr>
    </thead>
    <tbody>
      <tr>

        <td><img src="{{asset("$service->image")}}" width="500" height="300" alt=""></td>
        <td>{{$service->name}}</td>
        <td>{{substr($service->desc, 0,80)}}</td>

        <td>


            <form action="{{url("admin/services/delete/$service->id")}}" method="post">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">delete</button>
            </form>
        </td>
        <td>
            <h1>
                <a class="btn btn-success"  href="{{url("admin/services/edit/$service->id")}}"> edit </a>

            </h1>
        </td>
    </tr>


    </tbody>
  </table>

@endsection