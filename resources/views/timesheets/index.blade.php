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

                @foreach($timesheets as $timesheet)

                    @if($timesheet->status->last()->slug == $status->slug)
                        <div class="row mt-2">

                            @if($status->editable || $status->submittable)
                            <div class="col-3">
                                <div class="btn-group">
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
                                </div>
                            </div>
                            @endif
                            <div class="col-3"><h5>{{ date("F j, Y", strtotime($timesheet->start)) }}</h5></div>
                            <div class="col-3"><h5>{{ date("F j, Y", strtotime($timesheet->end)) }}</h5></div>
                            <div class="col-3"><h5>{{ intdiv($timesheet->timeworked, 60) }} hours {{ (int)$timesheet->timeworked % 60 }} minutes</h5></div>
                        </div>
                    @endif
                @endforeach
                </div>
            @endforeach
        </div>

@endsection

@section('footer_scripts')
@endsection
