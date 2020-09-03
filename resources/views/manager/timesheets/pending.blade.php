@extends('layouts.app')

@section('template_title')
    {{ Auth::user()->name }}'s' Timesheets
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
                        <div class="col-3 text-right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary">View</button>

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
