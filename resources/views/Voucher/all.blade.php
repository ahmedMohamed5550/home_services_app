@extends('layout')
@section('body')
@include('succsess')
<table class="table">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">Code</th>
        <th scope="col">Type</th>
        <th scope="col">Discount</th>
        <th scope="col">Status</th>
        <th scope="col">expired_at</th>
        <th scope="col">show </th>
      </tr>
    </thead>
    <tbody>
        @foreach ($vouchers as $voucher )
      <tr>
          <th scope="row">{{$loop->iteration}}</th>
          <td>{{ $voucher->code }}</td>
          <td>{{$voucher->type}}</td>
          <td> {{ $voucher->discount }}</td>
          <td> {{ $voucher->status}}</td>
          <td> {{ $voucher->expired_at}}</td>

        <td>
            <a class="btn btn-success" href="{{url("admin/voucher/show/$voucher->id")}}" >show</a>
            {{-- <a class="btn btn-success" href="{{url("admin/services/edit/$service->id")}}" >edit</a> --}}

        </td>
    </tr>
    @endforeach

    </tbody>
  </table>


@endsection