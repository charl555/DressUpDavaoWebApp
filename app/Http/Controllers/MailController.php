<?php

namespace App\Http\Controllers;

use App\Mail\AccountRegisteredMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public static function sendRegistrationEmail($user)
    {
        Mail::to($user->email)->send(new AccountRegisteredMail($user->name));
    }
}
