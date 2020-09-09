<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\Timesheet;
use App\Models\Status;
use App\Models\Comment;
use App\Models\Entry;
use App\Models\Hourtype;
use App\Models\HourtypeTimesheet;
use DateTime;
use DatePeriod;
use DateInterval;
use App\Models\Employeemanager;
use App\Models\User;

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
        $timesheets = Timesheet::where('user_id', Auth::user()->id)->orderBy('start', 'desc')->get();
        $statuses = Status::orderBy('sort_order', 'asc')->get();

        foreach ($timesheets as $timesheet) {
            $timesheet->timeworked = $this->total_timeworked($timesheet->id);
            $timesheet->pto = $this->get_timesheet_pto(($timesheet->id));
        }

        return view('timesheets.index', compact(['timesheets', 'statuses']));
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
     * @param \Illuminate\Http\Request $request
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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $timesheet = Timesheet::where('id', $id)->with('comments')->with('hours')->first();

        $day_after = new DateTime($timesheet->end . ' +1 day');

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

        foreach ($timesheet->comments as $comment) {
            $commentor = User::where('id', $comment->user_id)->first();
            $timesheet_data['comments'][$comment->id] = $comment;
            $timesheet_data['comments'][$comment->id]['user'] = $commentor;
        }

        $status = $timesheet->status;
        $decoded_status = json_decode($status, true);
        $timesheet_data['current_status'] = array_pop($decoded_status);

        $timesheet_data['submittable'] = false;

        foreach (new DatePeriod(new DateTime($timesheet->start), DateInterval::createFromDateString('1 day'), new DateTime($timesheet->day_after->format('Y-m-d'))) as $dt) {
            $todays_entries = [];
            $day_minutes = 0;
            $start_time = '';
            $end_time = '';
            $set_start = 0;
            $set_end = 0;

            if (count($entries) % 2 == 0 && ($timesheet_data['current_status']['slug'] == 'new' || $timesheet_data['current_status']['slug'] == 'returned')) {
                $timesheet_data['submittable'] = true;
            }

            foreach ($entries as $entry) {
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

        $hourtypes = Hourtype::pluck('name', 'id'); // This is only being used to generate a select box
        $hours = HourtypeTimesheet::where('timesheet_id', $timesheet->id)->get();
        $total_pto = 0;

        foreach ($hours as $hr) {
            $tp = Hourtype::where('id', $hr->hourtype_id)->first();
            $hr->hourtype_name = $tp->name;
            $hr->hourtype_slug = $tp->slug;
            $hr->hourtype_description = $tp->description;

            $total_pto = $total_pto + $hr->hours;
        }

        $timesheet_data['total_pto'] = $total_pto;
        $timesheet_data['total_pto_time'] = $this->time_to_decimal($total_pto);

        return view('timesheets.view', compact(['timesheet_data', 'hourtypes', 'hours']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $timesheet = Timesheet::where('id', $id)->with('comments')->with('hours')->first();

        $day_after = new DateTime($timesheet->end . ' +1 day');

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

        if (!$timesheet_data['current_status']['editable']) {
            return redirect('/timesheet/' . $timesheet->id);
        }

        foreach ($timesheet->comments as $comment) {
            $commentor = User::where('id', $comment->user_id)->first();
            $timesheet_data['comments'][$comment->id] = $comment;
            $timesheet_data['comments'][$comment->id]['user'] = $commentor;
        }

        $timesheet_data['submittable'] = false;
        //$timesheet_data['editable'] = $status->editable;

        foreach (new DatePeriod(new DateTime($timesheet->start), DateInterval::createFromDateString('1 day'), new DateTime($timesheet->day_after->format('Y-m-d'))) as $dt) {
            $todays_entries = [];
            $day_minutes = 0;
            $start_time = '';
            $end_time = '';
            $set_start = 0;
            $set_end = 0;

            if (count($entries) % 2 == 0 && ($timesheet_data['current_status']['slug'] == 'new' || $timesheet_data['current_status']['slug'] == 'returned')) {
                $timesheet_data['submittable'] = true;
            }

            foreach ($entries as $entry) {
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

        $hourtypes = Hourtype::pluck('name', 'id'); // This is only being used to generate a select box
        $hours = HourtypeTimesheet::where('timesheet_id', $timesheet->id)->get();
        $total_pto = 0;

        foreach ($hours as $hr) {
            $tp = Hourtype::where('id', $hr->hourtype_id)->first();
            $hr->hourtype_name = $tp->name;
            $hr->hourtype_slug = $tp->slug;
            $hr->hourtype_description = $tp->description;

            $total_pto = $total_pto + $hr->hours;
        }

        $timesheet_data['total_pto'] = $total_pto;
        $timesheet_data['total_pto_time'] = $this->time_to_decimal($total_pto);

        return view('timesheets.edit', compact(['timesheet_data', 'hourtypes', 'hours']));
    }

    // This should take a timesheet ID and return the number of minutes of PTO being used on that timesheet.
    public function get_timesheet_pto($timesheet_id)
    {
        $all_pto = HourtypeTimesheet::where('timesheet_id', $timesheet_id)->get();
        $total_pto = 0;
        foreach ($all_pto as $pto) {
            $total_pto = $total_pto + $pto->hours;
        }
        return $this->time_to_decimal($total_pto);
    }

    public function time_to_decimal($time)
    {
        $timeArr = explode('.', number_format($time, 2));
        $decTime = ($timeArr[0] * 60) + ($timeArr[1] / 100 * 60);
        return $decTime;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function add_comment($id, Request $request)
    {
        $timesheet = Timesheet::where('id', $id)->first();
        $comment = Comment::create($request->all());
        $timesheet->comments()->attach($comment->id);

        return redirect($request->redirect_to);
    }

    public function add_hours($id, Request $request)
    {
        $timesheet = Timesheet::where('id', $id)->first();
        $hourtype = Hourtype::where('id', $request->hourtype)->first();
        $timesheet->hours()->attach($hourtype, ['hours' => $request->hours, 'notes' => $request->notes, 'updated_at' => now(), 'created_at' => now()]);
        return redirect($request->redirect_to);
    }

    public function delete_hours($timesheet_id, $hours_id, Request $request)
    {
        $timesheet = Timesheet::where('id', $timesheet_id)->first();
        $hr = HourtypeTimesheet::find($hours_id);
        $timesheet->hours()->detach($hours_id);
        $hr->delete();
        return redirect($request->redirect_to);
    }


    public function submit_timesheet($id)
    {
        $timesheet = Timesheet::where('id', $id)->with('status')->first();
        $pending_status = Status::where('slug', 'pending')->first();
        $timesheet->status()->attach($pending_status->id);

        $comment = new Comment;
        $comment->user_id = Auth::user()->id;
        $comment->timesheet_id = $timesheet->id;
        $comment->comment = 'Timesheet Submitted';
        $comment->save();
        $timesheet->comments()->attach($comment->id);

        return redirect('/timesheet');
    }

    public function deny_timesheet($id)
    {
        $timesheet = Timesheet::where('id', $id)->with('status')->first();
        $pending_status = Status::where('slug', 'denied')->first();
        $timesheet->status()->attach($pending_status->id);

        $comment = new Comment;
        $comment->user_id = Auth::user()->id;
        $comment->timesheet_id = $timesheet->id;
        $comment->comment = 'Timesheet Denied';
        $comment->save();
        $timesheet->comments()->attach($comment->id);

        return redirect('/manager/timesheet/pending');
    }

    public function return_timesheet($id)
    {
        $timesheet = Timesheet::where('id', $id)->with('status')->first();
        $pending_status = Status::where('slug', 'returned')->first();
        $timesheet->status()->attach($pending_status->id);

        $comment = new Comment;
        $comment->user_id = Auth::user()->id;
        $comment->timesheet_id = $timesheet->id;
        $comment->comment = 'Timesheet Returned';
        $comment->save();
        $timesheet->comments()->attach($comment->id);

        return redirect('/manager/timesheet/pending');
    }

    public function approve_timesheet($id)
    {
        $timesheet = Timesheet::where('id', $id)->with('status')->first();
        $pending_status = Status::where('slug', 'approved')->first();
        $timesheet->status()->attach($pending_status->id);

        $comment = new Comment;
        $comment->user_id = Auth::user()->id;
        $comment->timesheet_id = $timesheet->id;
        $comment->comment = 'Timesheet Approved';
        $comment->save();
        $timesheet->comments()->attach($comment->id);

        return redirect('/manager/timesheet/pending');
    }


    public function pending_timesheet_manager()
    {
        $employees = Employeemanager::where('manager_user_id', Auth::user()->id)->get();

        $emp_ts_data = [];

        foreach ($employees as $emp) {
            $timesheets = Timesheet::where('user_id', $emp->employee_user_id)->with('status')->with('comments')->get();

            if ($timesheets->count() > 0) {
                $timesheets = $timesheets->filter(function ($ts) {
                    return $ts->status->last()->slug == 'pending';
                });

                $emp_ts_data[$emp->id] = $emp;
                $emp_ts_data[$emp->id]['employee_data'] = User::where('id', $emp->employee_user_id)->first();
                $emp_ts_data[$emp->id]['timesheets'] = $timesheets;

                foreach ($emp_ts_data[$emp->id]['timesheets'] as $ts) {
                    $time_worked = $this->total_timeworked($ts->id);
                    $ts->timeworked = $time_worked;
                }
            }
        }

        return view('manager.timesheets.pending', compact('emp_ts_data'));
    }

    public function manager_view_timesheet($id)
    {
        $timesheet = Timesheet::where('id', $id)->with('status')->with('comments')->first();
        $employee_data = User::where('id', $timesheet->user_id)->first();
        $timeworked = $this->total_timeworked($id);

        return view('manager.timesheets.view', compact(['employee_data', 'timesheet', 'timeworked']));
    }

    public function timesheet_manager()
    {

    }


    public function total_timeworked($id)
    {
        $timesheet = Timesheet::where('id', $id)->first();

        $day_after = new DateTime($timesheet->end . ' +1 day');

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

        foreach (new DatePeriod(new DateTime($timesheet->start), DateInterval::createFromDateString('1 day'), new DateTime($timesheet->day_after->format('Y-m-d'))) as $dt) {
            $todays_entries = [];
            $day_minutes = 0;
            $start_time = '';
            $end_time = '';
            $set_start = 0;
            $set_end = 0;

            foreach ($entries as $entry) {
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

            $timesheet_data['total_minutes'] = $timesheet_data['total_minutes'] + $day_minutes;

        }

        return $timesheet_data['total_minutes'];
    }

}
