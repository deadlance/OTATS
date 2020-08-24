<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Timesheet;
use App\Entry;
use DateTime;
use DatePeriod;
use DateInterval;

class TimesheetController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        // We want to return all of the available timesheets - a user should only get theirs, while a manager should get all of their employees
        // $user = Auth::user();
        $timesheets = Timesheet::where('user_id', Auth::user()->id)->get();

        return view('timesheets.index', compact('timesheets'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('timesheets.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $t = Timesheet::create($request->all());
        $t->status()->attach(1);
        return redirect('/timesheet/' . $t->id . '/edit');
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
        $timesheet = Timesheet::where('id', $id)->first();

        $day_after = new DateTime($timesheet->end .  ' +1 day');

        $timesheet->day_after = $day_after;

        $entries = Entry::where('timesheet_id', $timesheet->id)->get();
        $entries = $entries->sortBy('activity');

        // Each timesheet is a series of days, each containing any number of entries. Entries alternate between clock in and clock out.
        // So, what we need to do, is get all the entries for a timesheet and go through them, day by day, adding up the time.

        $startdate = new DateTime($timesheet->start);



        $timesheet_data = [
            "id" => $timesheet->id,
            "start_date" => $startdate->format('Y-m-j'),
            "last_date" => $timesheet->day_after->format('Y-m-j'),
            "total_minutes" => 0,
            "dailies" => [],
        ];

        $status = $timesheet->status;
        $decoded_status = json_decode($status, true);
        $timesheet_data['current_status'] = array_pop($decoded_status);

        $timesheet_data['submittable'] = false;

        foreach(new DatePeriod(new DateTime($timesheet->start), DateInterval::createFromDateString('1 day'), new DateTime($timesheet->day_after->format('Y-m-d'))) as $dt) {
            $todays_entries = [];
            $day_minutes = 0;
            $start_time = '';
            $end_time = '';
            $set_start = 0;
            $set_end = 0;

            if(count($entries) % 2 == 0 && $timesheet_data['current_status']['slug'] == 'new') {
                $timesheet_data['submittable'] = true;
            }

            foreach($entries as $entry) {
                if ($dt->format("Y-m-j") == date("Y-m-d", strtotime($entry->activity))) {
                    array_push($todays_entries, $entry);

                    // if start time is not empty and end time is empty...
                    if ($set_start != 0 && $set_end == 0) {
                        $end_time = new DateTime($entry->activity);
                        $set_end = 1;
                    }

                    // if start time and end time are empty...
                    if ($set_start == 0) {
                        $start_time = new DateTime($entry->activity);
                        $set_start = 1;
                    }

                    // If neither are empty...
                    if ($set_start != 0 && $set_end != 0) {
                        $diff = $start_time->diff($end_time);
                        $day_minutes = $day_minutes + (($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i);
                        $start_time = '';
                        $end_time = '';
                        $set_start = 0;
                        $set_end = 0;
                    }
                }
            }

            $timesheet_data['dailies'][$dt->format('Y-m-j')]['date'] = $dt->format('Y-m-j');
            $timesheet_data['dailies'][$dt->format('Y-m-j')]['entries'] = $todays_entries;
            $timesheet_data['dailies'][$dt->format('Y-m-j')]['minutes'] = $day_minutes;
            $timesheet_data['total_minutes'] = $timesheet_data['total_minutes'] + $day_minutes;

        }

        return view('timesheets.edit',compact('timesheet_data'));
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
    public function destroy($id)
    {
        //
    }
}
