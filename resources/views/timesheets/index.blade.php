@extends('layouts.app')

@section('template_title')
    {{ Auth::user()->name }}'s' Timesheets
@endsection

@section('template_fastload_css')
@endsection

@section('content')

    <div class="container">


        <h3>Timesheets</h3>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs">
            @foreach($statuses as $status)
                <li class="nav-item">
                    @if($loop->first)
                        <a class="nav-link active" data-toggle="tab" href="#{{ $status->slug }}">{{ $status->name }}</a>
                    @else
                        <a class="nav-link" data-toggle="tab" href="#{{ $status->slug }}">{{ $status->name }}</a>
                    @endif
                </li>
            @endforeach
        </ul>

        <div class="tab-content">
            @foreach($statuses as $status)
                @if($loop->first)
                    <div class="tab-pane container active" id="{{ $status->slug }}">
                @else
                    <div class="tab-pane container" id="{{ $status->slug }}">
                @endif
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Hours Worked</th>
                            <th>PTO Used</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($timesheets as $timesheet)

                            @if($timesheet->status->last()->slug == $status->slug)
                                <tr>

                                    <td>&nbsp;
                                        <div class="btn-group">
                                            @if($status->editable || $status->submittable)
                                                @if($status->editable)
                                                    <a href="/timesheet/{{ $timesheet->id }}/edit" class="btn btn-info" role="button">Edit</a>
                                                @endif
                                                @if($status->submittable)
                                                    <form action="/timesheet/submit/{{ $timesheet->id }}" method="get">
                                                        @csrf
                                                        <input type="hidden" name="timesheet_id" value="{{ $timesheet->id }}">
                                                        <button type="submit" class="btn btn-success">Submit</button>
                                                    </form>
                                                @endif
                                            @else
                                                <a href="/timesheet/{{ $timesheet->id }}" class="btn btn-success" role="button">View</a>
                                            <button type="button" class="btn btn-warning">Download</button>
                                        @endif
                                        </div>
                                    </td>
                                    <td><h5>{{ date("F j, Y", strtotime($timesheet->start)) }}</h5></td>
                                    <td><h5>{{ date("F j, Y", strtotime($timesheet->end)) }}</h5></td>
                                    <td><h5>{{ intdiv($timesheet->timeworked, 60) }} hours {{ (int)$timesheet->timeworked % 60 }} minutes</h5></td>
                                    <td><h5>{{ intdiv($timesheet->pto, 60) }} hours {{ (int)$timesheet->pto % 60 }} minutes</h5></td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>

@endsection

@section('footer_scripts')
@endsection
