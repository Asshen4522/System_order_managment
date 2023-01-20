<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class userController extends Controller
{
    public function Authorizate(Request $request)
    {
        if (Auth::attempt($request->only('phone', 'password'))) {
            return true;
        } else {
            return false;
        }
    }
}
