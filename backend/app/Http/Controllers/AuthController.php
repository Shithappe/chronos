<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        $fields = $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
            'email' => 'email:rfc,dns'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'password' => bcrypt($fields['password']),
            'email' => $fields['email']
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function login(Request $request) {
        $fields = $request->validate([
            'email' => 'email:rfc,dns',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $fields['email'])->first();

        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Bad'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout(Request $request) {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged out'
        ];
    }

    public function reset_pass(Request $request) { // for dev
        $request->validate([
            'email' => 'email:rfc,dns',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $request['email'])->first();

        if(!$user) return response('Wrong email');

        $request['password'] = bcrypt($request['password']);
        return response($user->update($request->all()), 201);
    }

    public function reset_password(Request $request){ // for users
        $user = auth()->user();
        if(!$user) return response('link is invalid'); // here some prb
        $request['password'] = bcrypt($request['password']);
        $user->update($request->all());
        auth()->user()->tokens()->delete();
        return $user;
    }
}
