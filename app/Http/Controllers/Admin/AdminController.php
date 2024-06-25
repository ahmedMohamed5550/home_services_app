<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function showRegisterForm()
    {
        return view("adminRegister");
    }


    public function register(Request $request)
    {
        // echo "hi admin";
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string',
            'password' => 'required|string|min:6',
            'userType' => 'required|string|in:admin',
            'name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Hash the password
        $data = $request->only('email', 'password', 'userType', 'name', 'phone');
        $data['password'] = Hash::make($data['password']);


        // Create the user
        User::create($data);

        // Redirect to login page with a success message
        return redirect(url('admin/employee/all'))->with('success', 'User registered successfully');
    }

    public function showLoginForm()
    {
        return view("adminLogin");
        // echo "islam";
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Attempt to log the admin in
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->userType == "admin") {
                session(['user' => $user]);
                return redirect(url('admin/redirect'))->with('success', 'User login successfully');
            } else {

                return redirect()->back()->withErrors(['email' => 'The provided credentials do not match our records.'])->withInput();
            }
        } else {
            return redirect()->back()->withErrors(['email' => 'The provided credentials do not match our records.'])->withInput();
        }
    }

    public function redirect()
    {
        return redirect('admin/employee/all');
    }

    public function logout(Request $request)
    {
        // Auth::logout();

        // Flush the session data
        Session::flush();

        // Redirect to the login page with a success message
        return redirect(url('/admin/login'))->with('success', 'Logged out successfully');
    }
}
