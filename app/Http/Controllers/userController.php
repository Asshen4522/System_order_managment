<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\user;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class userController extends Controller
{
    public function Authorizate(Request $request)
    {
        if (Auth::attempt($request->only('phone', 'password'))) {
            $answer = auth()->user()->role_id;
            return $answer;
        } else {
            $answer = 'false';
            return $answer;
        }
    }

    public function Register(Request $request)
    {
        try {
            user::create([
                'name' => $request->name,
                'surname' => $request->surname,
                'phone' => $request->phone,
                'role_id' => $request->roleId,
                'password' => Hash::make($request->password)
            ]);
            $answer = 'true';
            return $answer;
        } catch (\Throwable $th) {
            $answer = 'false';
            return $answer;
        }
    }
}
