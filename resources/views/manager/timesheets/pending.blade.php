@extends('layouts.app')

@section('template_title')
    {{ Auth::user()->name }}'s' Timesheets
@endsection

@section('template_fastload_css')
@endsection

@section('content')

    <div class="container">


        <h3>Pending Timesheets</h3>


        @foreach($emp_ts_data as $emp)

            <div class="row border pb-2 pt-2">
                <div class="col-12">
                    {{ $emp['employee_data']['first_name'] }} {{ $emp['employee_data']['last_name'] }}
                </div>
            </div>
                @foreach($emp['timesheets'] as $timesheet)
                    <div class="row pb-2 pt-2">
                        <div class="col-4">
                            {{ date('d M Y', strtotime($timesheet->start)) }} through {{ date('d M Y', strtotime($timesheet->end)) }}
                        </div>
                        <div class="col-3">
                            Submitted {{ $timesheet->status[0]->created_at }}
                        </div>
                        <div class="col-2">
                            {{ intdiv($timesheet->timeworked,60) }} hours {{ $timesheet->timeworked % 60 }} minutes
                        </div>
                        <div class="col-2 text-right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary">View</button>
                                <button type="button" class="btn btn-danger">Deny</button>
                                <button type="button" class="btn btn-success">Approve</button>
                            </div>
                        </div>
                    </div>

                @endforeach

        @endforeach

@endsection

@section('footer_scripts')
@endsection
