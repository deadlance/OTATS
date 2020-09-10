@extends('layouts.app')

@section('template_title')
    Timesheets
@endsection

@section('template_fastload_css')
@endsection

@section('content')

    <div class="container">


        <h3>Timesheets</h3>

        <!--
        <pre>
            {{ json_encode($employees, JSON_PRETTY_PRINT) }}
        </pre>
        -->
        <div id="accordion">

            @foreach($employees as $emp)

                <div class="card">
                    <div class="card-header">
                        <a class="card-link" data-toggle="collapse" href="#user{{ $emp->emp_data->id }}card">
                            {{ $emp->emp_data->first_name }} {{ $emp->emp_data->last_name }}
                        </a>
                    </div>

                    @if($loop->index == 0)
                        <div id="user{{ $emp->emp_data->id }}card" class="collapse show" data-parent="#accordion">
                    @else
                        <div id="user{{ $emp->emp_data->id }}card" class="collapse" data-parent="#accordion">
                    @endif
                        <div class="card-body">

                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Start / End Dates</th>
                                    <th>Status</th>
                                    <th>Time Worked</th>
                                    <th>PTO</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($emp->timesheets as $timesheet)
                                        <tr>
                                            <td>{{ $timesheet->start }} - {{ $timesheet->end }}</td>
                                            <td>{{ $timesheet->current_status->name }}</td>
                                            <td>{{ intdiv($timesheet->time_worked, 60) }} hours {{ $timesheet->time_worked % 60 }} minutes</td>
                                            <td>{{ intdiv($timesheet->pto, 60) }} hours {{ $timesheet->pto % 60 }} minutes</td>
                                            <td><a href="/timesheet/{{ $timesheet->id }}" class="btn btn-primary" role="button">View</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>

            @endforeach

        </div>

    </div>
@endsection

@section('footer_scripts')
@endsection
