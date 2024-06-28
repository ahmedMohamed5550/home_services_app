@extends("layout")
@section("body")
@include('errors')
@include('succsess')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
      <div class="card corona-gradient-card">
        <div class="card-body py-0 px-0 px-sm-3">
        </div>
      </div>
    </div>
  </div>

  <div class="row ">
    <div class="col-12 grid-margin">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Employees Status</h4>
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>
                    <div class="form-check form-check-muted m-0">
                      <label class="form-check-label">
                        <input type="checkbox" class="form-check-input">
                      </label>
                    </div>
                  </th>
                  <th> Employee Name </th>
                  <th> Employee Email </th>
                  <th> Employee phone </th>
                  <th> Desc</th>
                  <th> min_price </th>
                  {{-- <th>image SSN  </th> --}}
                  {{-- <th> live photo </th> --}}
                  <th> national ID </th>
                  <th> Status </th>
                  {{-- <th> checkByAdmin </th> --}}
                  <th> Show All Data </th>
                </tr>
              </thead>
              <tbody>

                @foreach ( $employees as $employee  )
                <tr>
                    <td>
                      <div class="form-check form-check-muted m-0">
                        <label class="form-check-label">
                          <input type="checkbox" class="form-check-input">
                        </label>
                      </div>
                    </td>
                    <td>
                      {{-- <img class="" src="{{asset("storage/$employee->imageSSN")}}" alt="image" /> --}}
                      <span class="ps-2">{{ $employee->name }}</span>
                    </td>
                    <td> {{ $employee->email }} </td>
                    <td> {{ $employee->phone  }}</td>
                    <td> {{ $employee->desc  }} </td>
                    <td> {{ $employee->min_price  }} </td>
                    {{-- <td> <img class="" src="{{asset("storage/$employee->imageSSN")}}" alt="image" /></td> --}}
                    {{-- <img class="" src="{{ asset('storage/' . $employee->livePhoto) }}" alt="image" /> --}}
                    <td>{{ $employee->nationalId  }} </td>
                    <td> {{ $employee->checkByAdmin  }} </td>
                    {{-- <td><a href="{{ url("admin/checkByAdmin/{$employee->id}/accepted") }}" class="badge badge-outline-success">Accepted</a></td> --}}
                    {{-- <td><a href="{{ url("admin/checkByAdmin/{$employee->id}/rejected") }}" class="badge badge-outline-danger">Rejected</a></td> --}}
                    <td><a href="{{ url("admin/employee/show/$employee->id")}}" class="badge badge-outline-warning">show</a></td>

                  </tr>


                @endforeach



              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>


@endsection