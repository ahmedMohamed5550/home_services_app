@extends('layout')
@section('body')
@include('succsess')
<table class="table">
    <thead>
      <tr>
        <th scope="col">Code</th>
        <th scope="col">Type</th>
        <th scope="col">Discount</th>
        <th scope="col">Status</th>
        <th scope="col">expired_at</th>
        <th scope="col">Edit </th>
        <th scope="col">Delete </th>
      </tr>
    </thead>
    <tbody>
      <tr>
          <td>{{ $voucher->code }}</td>
          <td>{{$voucher->type}}</td>
          <td> {{ $voucher->discount }}</td>
          <td> {{ $voucher->status}}</td>
          <td> {{ $voucher->expired_at}}</td>

        <td>
            <a class="btn badge btn-outline-warning" href="{{url("admin/voucher/edit/$voucher->id")}}" >Edit</a>
            {{-- <a class="btn btn-success" href="{{url("admin/services/edit/$service->id")}}" >edit</a> --}}

        </td>
        <td>
            <form action="{{url("admin/voucher/delete/$voucher->id")}}" method="post">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">delete</button>
            </form>
        </td>
    </tr>

    </tbody>
  </table>


@endsection