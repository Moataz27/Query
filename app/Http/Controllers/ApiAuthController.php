<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiAuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make([
            'user_name'=> 'string|alpha_num|required|min:5|max:60|unique:users,user_name',
            'full_name' => 'string|max:255|required',
            'profile_picture' => 'nullable|file|mime:jpg,jpeg,jfif,png, max:4096',
            'email' => 'email:rfc,dns|required|unique:users,email',
            'password' => 'confirmed|required|min:8|max:255|alpha_dash'
        ]);
    }
}
