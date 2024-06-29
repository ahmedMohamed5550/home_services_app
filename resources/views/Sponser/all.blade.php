@extends('layout')
@section('body')
    @include('succsess')
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">image</th>
                <th scope="col">title</th>
                <th scope="col">desc</th>
                <th scope="col">type</th>
                <th scope="col">expired_at</th>
                <th scope="col">show </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sponsers as $sponser)
                <tr>
                    <th scope="row">{{ $loop->iteration }}</th>
                    <td><img src="{{ asset("$sponser->image") }}" width="500" height="300" alt=""></td>
                    <td>{{ $sponser->title }}</td>
                    <td> {{ $sponser->desc }}</td>
                    <td> {{ $sponser->type }}</td>
                    <td> {{ $sponser->expired_at }}</td>

                    <td>
                        <a class="btn btn-success" href="{{ url("admin/sponser/show/$sponser->id") }}">show</a>
                        {{-- <a class="btn btn-success" href="{{url("admin/services/edit/$service->id")}}" >edit</a> --}}

                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>
@endsection
