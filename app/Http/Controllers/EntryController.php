<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\Timesheet;
use App\Models\Entry;
use DateTime;


class EntryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /*
        $t = Timesheet::create($request->all());
        $t->status()->attach(1);
        return redirect('/timesheet/' . $t->id . '/edit');
        */


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $timesheet_id = $request->timesheet_id;
        $entry_date = $request->entry_date;
        $activity = new DateTime($entry_date . ' ' . $request->activity);
        $comments = $request->comments;

        // If the time is a duplicate, we need to update the comments, not add a new entry.
        $exists = Entry::where('timesheet_id', $timesheet_id)->where('activity', $activity)->first();
        if($exists) {
            $entry = $exists;
            $entry->comments = $comments;
            $entry->save();
        }
        else {
            $entry = new Entry;
            $entry->timesheet_id = $timesheet_id;
            $entry->activity = $activity;
            $entry->comments = $comments;
            $entry->save();
        }

        $timesheet = Timesheet::where('id', $timesheet_id)->first();
        return redirect('/timesheet/' . $timesheet->id . '/edit');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Entry $entry)
    {
        $timesheet_id = $entry->timesheet_id;
        $entry->delete();

        $timesheet = Timesheet::where('id', $timesheet_id)->first();
        return redirect('/timesheet/' . $timesheet->id . '/edit');


    }

    /**
     * Calculate the amount of time between clock_in and clock_out
     *
     */
    public function time_worked(DateTime $clock_in, DateTime $clock_out)
    {
        $diff = $clock_in->diff($clock_out);
        return ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
    }
}
