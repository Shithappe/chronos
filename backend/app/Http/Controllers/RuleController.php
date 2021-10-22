<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rule;
use Illuminate\Http\Request;

class RuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'calendar_id' => 'required',
            'access' => 'required|string'
        ]);

        $rule = Rule::create([
            'user_id' => $request['user_id'],
            'calendar_id' => $request['calendar_id'],
            'access' => $request['access'],
            // 'user_id' => auth()->user()->id
        ]);
        
        return $rule;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Rule  $rule
     * @return \Illuminate\Http\Response
     */
    public function show(Rule $rule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Rule  $rule
     * @return \Illuminate\Http\Response
     */
    public function edit(Rule $rule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Rule  $rule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Rule $rule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Rule  $rule
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function accept_rule($calendar_id, $user_id){
        $rule = Rule::where('calendar_id', $calendar_id)->where('user_id', $user_id)->update(['accepted' => true]);
        return response($rule, 201);
    }

    public function update_calendar_member(Request $request){
        $request->validate([
            'email' => 'email:rfc,dns',
            'calendar_id' => 'required|numeric',
            'access' => 'required|string'
        ]);

        $user_id = User::where('email', $request['email'])->get('id');
        $request['user_id'] = $user_id[0]['id'];

        $rule = Rule::where('user_id', $request['user_id'])->where('calendar_id', $request['calendar_id'])->first();
        if (!$rule) return reponse('not rule', 403);
        $rule->update($request->all());
        return $rule;
    }

    public function destroy_calendar_member($calendar_id, $user_id){

        $rule = Rule::where('user_id', $user_id)->where('calendar_id', $calendar_id)->first();
        if (!$rule) return reponse('not rule', 403);
        return $rule->delete();
    }
}
