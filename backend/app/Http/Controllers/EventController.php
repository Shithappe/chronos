<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($calendar_id)
    {
        return Event::where('calendar_id', $calendar_id)->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required|string',
            'calendar_id' => 'required|numeric',
            'backgroundColor' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date',
            'allDay' => 'boolean'
        ]);
        
        $event = Event::create([
            'title' => $fields['title'],
            'calendar_id' => $fields['calendar_id'],
            'backgroundColor' => $fields['backgroundColor'],
            'start' => $fields['start'],
            'end' => $fields['end'],
            'allDay' => $fields['allDay']
        ]);
        
        return $event;
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
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $result = $this->error_accept_to_event($id);
        if ($result) return $result;

        $event = Event::find($id);
        $event->update($request->all());
        return $event;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->error_accept_to_event($id);
        if ($result) return $result;
        
        Event::destroy($id);
        return response("ok", 201);
    }

    private function error_accept_to_event($id){
        $calendars = app()->call('App\Http\Controllers\CalendarController@show_my_caledars');
        if ($calendars->isEmpty()) return response('Calendar not exist', 403);

        $calendar_id = [];
        foreach ($calendars as $key) {
            $calendar_id[] = $key['id'];
        }

        $event = Event::where('id', $id)->get();
        if ($event->isEmpty()) return response('event dont exict', 403);

        if (!in_array($event[0]['calendar_id'], $calendar_id)) return response('dont accept', 403);
        return false;
    }
}
