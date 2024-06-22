@extends('layout')
@section('body')
@include('errors')
@include('succsess')

<form method="POST" action="{{url("admin/Voucher")}}" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
      <label for="exampleInputEmail1">Voucher Code</label>
      <input type="text" name="code" class="form-control text-white" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter voucher code" value="{{ old('voucher') }}" >
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Voucher Type</label>
        <select class="form-control text-white" name="type" id="">
            <option value="fixed">fixed</option>
            <option value="percent">percent</option>
        </select>
      </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Voucher Discount</label>
        <input type="number" name="discount" class="form-control text-white" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter voucher discount" value="{{ old('discount') }}" >
      </div>

      <div class="form-group">
        <label for="exampleInputEmail1">Voucher Status</label>
        <select class="form-control text-white" name="status" id="">
            <option value="active">active</option>
            <option value="used">used</option>
            <option value="expired">expired</option>
        </select>
      </div>

      <div class="form-group">
        <label for="exampleInputEmail1">Expired at</label>
        <input type="date" name="expired_at" class="form-control text-white" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter voucher discount" value="{{ old('expired_at') }}" >
      </div>

    <button type="submit" class="btn btn-primary">Submit</button>
</form>

@endsection