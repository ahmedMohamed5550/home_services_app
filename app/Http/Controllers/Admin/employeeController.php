<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class employeeController extends Controller
{
    public function getUserEmployeeData()
    {
        $employees = Employee::join('users', 'employees.user_id', '=', 'users.id')
            ->select('employees.*', 'users.name', 'users.email', 'users.phone')
            ->where('users.userType', 'employee')
            ->where('employees.checkByAdmin', 'waiting')
            ->get();
        return view("adminHome", compact("employees"));
    }



    public function checkByAdmin($employeeId, $status)
    {
        $employee = Employee::find($employeeId);

        if (!$employee) {
            return redirect(url('admin/employee/all'))->with('errors', 'Employee not found.');
        }

        $employee->checkByAdmin = $status;

        // if($employee->checkByAdmin == 'rejected'){
        //     $employee->delete();
        // }
        // else{
        //     $employee->save();
        // }   
        $employee->save();

        return redirect(url('admin/employee/all'))->with('succsess', 'Employee status updated successfully.');
    }

    public function showEmployee($id)
    {
        $employee = Employee::join('users', 'employees.user_id', '=', 'users.id')
            ->select('employees.*', 'users.name', 'users.email', 'users.phone')
            ->where('users.userType', 'employee')
            ->where('employees.checkByAdmin', 'waiting')
            ->where('employees.id', $id)
            ->get();
        return view("Employee.show", compact("employee"));
    }
}
