<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function showLogin()
    {
        return view('Login');
    }

    public function showRegister()
    {
        return view('Register');
    }

    public function showForgotPassword()
    {
        return view('ForgotPassword');
    }
}
