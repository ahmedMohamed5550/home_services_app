@extends('layout')
@section('body')
    @include('succsess')
    <table class="table">
        <thead>
            <tr>
                <th scope="col">image</th>
                <th scope="col">title</th>
                <th scope="col">desc</th>
                <th scope="col">type</th>
                <th scope="col">expired_at</th>
                <th scope="col">Edit </th>
                <th scope="col">Delete </th>
            </tr>
        </thead>
        <tbody>
            <tr>
              <td><img src="{{ asset("storage/$sponser->image") }}" width="500" height="300" alt=""></td>
              <td>{{ $sponser->title }}</td>
              <td> {{ $sponser->desc }}</td>
              <td> {{ $sponser->type }}</td>
              <td> {{ $sponser->expired_at }}</td>

                <td>
                    <a class="btn badge btn-outline-warning" href="{{ url("admin/sponser/edit/$sponser->id") }}">Edit</a>
                    {{-- <a class="btn btn-success" href="{{url("admin/services/edit/$service->id")}}" >edit</a> --}}

                </td>
                <td>
                    <form action="{{ url("admin/sponser/delete/$sponser->id") }}" method="post">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">delete</button>
                    </form>
                </td>
            </tr>

        </tbody>
    </table>
@endsection
