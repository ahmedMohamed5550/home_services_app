@extends('layout')
@section('body')
    @include('errors')
    @include('succsess')

    <form method="POST" action="{{ url("admin/sponser/edit/$sponser->id") }}" enctype="multipart/form-data">
        @csrf
        @method("put")
        <div class="form-group">
            <label for="exampleInputEmail1">Sponser title</label>
            <input type="text" name="title" class="form-control text-white" id="exampleInputEmail1"
                aria-describedby="emailHelp" placeholder="Enter sponser title" value="{{ $sponser->title }}">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">Sponser desc</label>
            <textarea type="text" name="desc" class="form-control text-white" id="exampleInputEmail1"
                aria-describedby="emailHelp" placeholder="Enter desc">{{ $sponser->desc }}</textarea>
        </div>
        <div class="form-group">
            <input type="file" name="image" class="form-control text-white" id="exampleInputEmail1"
                aria-describedby="emailHelp" placeholder="Enter image" value="{{ $sponser->image }}">
        </div>
     
        <div class="form-group">
            <label for="exampleInputEmail1">Sponser type</label>
            <select class="form-control text-white" name="type" id="">
                <option value="available">available</option>
                <option value="expired">expired</option>

            </select>
        </div>

        <div class="form-group">
            <label for="exampleInputEmail1">Expired at</label>
            <input type="date" name="expired_at" class="form-control text-white" id="exampleInputEmail1"
                aria-describedby="emailHelp" placeholder="Enter voucher discount"  value="{{ $sponser->expired_at }}">
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
@endsection
