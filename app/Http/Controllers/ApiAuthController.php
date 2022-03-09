<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class ApiAuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'user_name'=> 'string|alpha_num|required|min:5|max:60|unique:users,user_name',
            'full_name' => 'string|max:255|required',
            'profile_picture' => 'nullable|file|mimes:jpg,jpeg,jfif,png, max:4096',
            'email' => 'email:rfc,dns|required|unique:users,email',
            'password' => 'confirmed|required|min:8|max:255|alpha_dash'
        ]);

        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()
            ]);
        } else{
            $img = ($request->hasFile('profile_picture')) ? Storage::putFile('images', $request->profile_picture) : public_path('uploads/images/default.jfif');
            $imgPath = public_path('uploads') . $img;

            $access_token = Str::random(64);
            $user = User::create([
                'user_name'=> $request->user_name,
                'full_name'=> $request->full_name,
                'profile_picture'=> $imgPath,
                'email'=> $request->email,
                'password'=> Hash::make($request->password),
                'access_token' => $access_token
            ]);
            if($user){
                return response()->json([
                    'data' => $user
                ], 200);
            }
        }
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            "email" => 'required|email:rfc,dns',
            'password' => 'required|string|min:8|max:255|alpha_dash'
        ]);

        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors(),
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if($user !== null){
            if(Hash::check($request->password, $user->password)){
                $access_token = Str::random(64);
                $user->update([
                    'access_token' => $access_token
                ]);

                return response()->json([
                    'data' => $user
                ], 200);
            } else{
                return response()->json([
                    'error' => "Cerdantial Are incorrect",
                ], 200);
            }
        }else{
            return response()->json([
                'error' => "Cerdantial Are incorrect",
            ], 200);
        }
    }

    public function logout(Request $request){
        if($request->hasHeader('access_token')){
            $access_token = $request->header('access_token');
            $user = User::where('access_token', $access_token)->first();
            if($user){
                $user->update([
                    'access_token' => null,
                ]);
                return response()->json([
                    'msg' => "You Logged Out",
                ], 200);
            } else{
                return response()->json([
                    'error' => "Cerdantial Are incorrect",
                ], 200);
            }
        }
    }
}
