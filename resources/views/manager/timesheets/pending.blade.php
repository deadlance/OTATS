@extends('layouts.app')

@section('template_title')
    Pending Timesheets
@endsection

@section('template_fastload_css')
@endsection

@section('content')

    <div class="container">


        <h3>Pending Timesheets</h3>
<!--
        <pre>
            {{ json_encode($emp_ts_data, JSON_PRETTY_PRINT) }}
        </pre>
-->

        @foreach($emp_ts_data as $emp)

            <div class="row border pb-2 pt-2">
                <div class="col-12">
                    {{ $emp['employee_data']['first_name'] }} {{ $emp['employee_data']['last_name'] }}
                </div>
            </div>
            <div class="row pb-2 pt-2 border-bottom">
                <div class="col-3">
                    Time Frame
                </div>
                <div class="col-2">
                    Submitted
                </div>
                <div class="col-2">
                    Hours Worked
                </div>
                <div class="col-2">
                    PTO
                </div>
                <div class="col-3 text-right">
                    &nbsp;
                </div>
            </div>
                @foreach($emp['timesheets'] as $timesheet)
                    <div class="row pb-2 pt-2">
                        <div class="col-3">
                            {{ date('d M Y', strtotime($timesheet->start)) }} - {{ date('d M Y', strtotime($timesheet->end)) }}
                        </div>
                        <div class="col-2">
                            {{ $timesheet->status[0]->created_at }} <!-- submitted -->
                        </div>
                        <div class="col-2">
                            {{ intdiv($timesheet->timeworked,60) }} hours {{ $timesheet->timeworked % 60 }} minutes
                        </div>
                        <div class="col-2">
                            {{ intdiv($timesheet->pto,60) }} hours {{ $timesheet->pto % 60 }} minutes
                        </div>
                        <div class="col-3 text-right">
                            <div class="btn-group">
                                <a href="/timesheet/{{ $timesheet->id }}" class="btn btn-primary" role="button">View</a>

                                <form action="/timesheet/return/{{ $timesheet->id }}" method="get">
                                    @csrf
                                    <input type="hidden" name="timesheet_id" value="{{ $timesheet->id }}">
                                    <button type="submit" class="btn btn-warning">Return</button>
                                </form>

                                <form action="/timesheet/approve/{{ $timesheet->id }}" method="get">
                                    @csrf
                                    <input type="hidden" name="timesheet_id" value="{{ $timesheet->id }}">
                                    <button type="submit" class="btn btn-success">Approve</button>
                                </form>

                                <form action="/timesheet/deny/{{ $timesheet->id }}" method="get">
                                    @csrf
                                    <input type="hidden" name="timesheet_id" value="{{ $timesheet->id }}">
                                    <button type="submit" onClick="return confirm('Are you sure you wish to deny this timesheet?')" class="btn btn-danger">Deny</button>
                                </form>

                            </div>
                        </div>
                    </div>

                @endforeach

        @endforeach

@endsection

@section('footer_scripts')
@endsection
