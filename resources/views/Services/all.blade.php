@extends('layout')
@section('body')
@include('succsess')
<table class="table">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">Image</th>
        <th scope="col">desc</th>
        <th scope="col">Name</th>
        <th scope="col">Aciton</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($services as $service )
      <tr>
          <th scope="row">{{$loop->iteration}}</th>
          <td><img src="{{asset("$service->image")}}" width="500" height="300" alt=""></td>
        <td>{{$service->name}}</td>
        <td> {{ substr($service->desc, 0,30) }}</td>

        <td>
            <a class="btn btn-success" href="{{url("admin/services/show/$service->id")}}" >show</a>
            {{-- <a class="btn btn-success" href="{{url("admin/services/edit/$service->id")}}" >edit</a> --}}

        </td>
    </tr>
    @endforeach

    </tbody>
  </table>


@endsection