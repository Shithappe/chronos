<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\Rule;
use App\Models\User;
use App\Http\Controllers\MailController;

use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Calendar::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $fields = $request->validate([
            'description' => 'required|string'
        ]);

        $calendar = Calendar::create([
            'description' => $fields['description'],
            'user_id' => auth()->user()->id
        ]);
        
        return $calendar;
    }

    public function show_my_caledars() {
        $user_id = auth()->user()->id;
        $user_calendars_id = Calendar::where('user_id', $user_id)->get('id')->toArray();
        $user_calendars_id = $this->get_atribute_from_array($user_calendars_id, 'id');
        $user_access_calendars_id = Rule::where('user_id', $user_id)->get('calendar_id')->toArray();
        $user_access_calendars_id = $this->get_atribute_from_array($user_access_calendars_id, 'calendar_id');

        $calendars_id = array_merge($user_calendars_id, $user_access_calendars_id);
        return Calendar::whereIn('id', $calendars_id)->get();        
    }

    private function get_atribute_from_array (array $data, string $atribute_name = 'id') {
        $result = [];

        foreach ($data as $key) {
            $result[] = $key[$atribute_name];
        }

        return $result;
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
     * @param  \App\Models\Calendar  $calendar
     * @return \Illuminate\Http\Response
     */
    public function show(Calendar $calendar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Calendar  $calendar
     * @return \Illuminate\Http\Response
     */
    public function edit(Calendar $calendar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Calendar  $calendar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Calendar $calendar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Calendar  $calendar
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $calendar = Calendar::where('id', $id)->where('user_id', auth()->id())->get();
        // if ($calendar->isEmpty()) return response('Calendar not exist', 403);
        Calendar::destroy($id);
        return response('ok', 201);
    }

    public function share_calendar(Request $request){
        $request->validate([
            'email' => 'email:rfc,dns',
            'calendar_id' => 'required|numeric',
            'access' => 'required|string'
        ]);

        $user_id = User::where('email', $request['email'])->get('id');
        $request['user_id'] = $user_id[0]['id'];

        Rule::create($request->all());

        app()->call('App\Http\Controllers\MailController@share_calendar', [$request->all()]);
    }

    public function update_calendar_member(Request $request){
        $request->validate([
            'email' => 'email:rfc,dns',
            'calendar_id' => 'required|numeric',
            'access' => 'required|string'
        ]);

        $user_id = User::where('email', $request['email'])->get('id');
        $request['user_id'] = $user_id[0]['id'];

        $rule = Rule::where('user_id', $request['user_id'])->where('calendar_id', $request['calendar_id'])->get();
        $rule->update($request->all());
        return $rule;
    }
}
