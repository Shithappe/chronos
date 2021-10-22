<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class MailController extends Controller
{
    public function email_forgot_password(Request $request){

        $request->validate([
            'email' => 'email:rfc,dns'
        ]);

        $user = User::where('email', $request['email'])->first();
        if(!$user) return response('Wrong email');

        $token = $user->createToken('myapptoken')->plainTextToken; //мож зашифровать ещё?
        $link = 'http://127.0.0.1:8000/api/reset_password_form/' . $token;

        $data = [
        'title' => 'ты там пароль забыл, вроде',
        'body' => $link
    ];
    \Mail::to($user['email'])->send(new \App\Mail\resetPassMail($data));

    return "sended";
    }

    public function share_calendar(Request $request){

        $link = 'http://127.0.0.1:8000/api/accepted_to_calendar/' . $request['calendar_id'] . '/' . $request['user_id'];

        $data = [
            'title' => 'лови приглос в календарь',
            'body' => $link
        ];
        \Mail::to($request['email'])->send(new \App\Mail\shareCalendarMail($data));
    }
}
