<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function main(){
        return DB::select('select * from users');
    }

    public function main_post(Request $request){
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|string|unique:users,email|max:255',
            'password' => 'required',
        ]);
        $user = new User; 
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->save();
        return $user;
    }

    public function show($id)
    {
        return User::find($id);
    }

    public function search($name)
    {
        return User::where('name', 'like', '%'.$name.'%')->get();
    }

    public function reset_password(Request $request){
        return "adw";
    }
}
