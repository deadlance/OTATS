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
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#new">Current</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#pending">Pending</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#approved">Approved</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#denied">Denied</a>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div class="tab-pane container active" id="new">
                @foreach($timesheets as $timesheet)
                    @if($timesheet->status->last()->name == 'new')
                        <div class="row mt-2">
                            <div class="col-3">
                                <div class="btn-group">
                                    <a href="/timesheet/{{ $timesheet->id }}/edit" class="btn btn-info" role="button">Edit</a>
                                    <button type="button" class="btn btn-success">Submit</button>
                                </div>
                            </div>
                            <div class="col-3"><h5>{{ date("F j, Y", strtotime($timesheet->start)) }}</h5></div>
                            <div class="col-3"><h5>{{ date("F j, Y", strtotime($timesheet->end)) }}</h5></div>
                            <div class="col-3"><h5>Hours Worked</h5></div>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="tab-pane container fade" id="pending">
                @foreach($timesheets as $timesheet)
                    @if($timesheet->status->last()->name == 'pending')
                        <div class="row mt-2">
                            <div class="col-3">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-secondary">View</button>
                                </div>
                            </div>
                            <div class="col-3"><h5>{{ date("F j, Y", strtotime($timesheet->start)) }}</h5></div>
                            <div class="col-3"><h5>{{ date("F j, Y", strtotime($timesheet->end)) }}</h5></div>
                            <div class="col-3"><h5>Hours Worked</h5></div>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="tab-pane container fade" id="approved">
                @foreach($timesheets as $timesheet)
                    @if($timesheet->status->last()->name == 'approved')
                        <div class="row mt-2">
                            <div class="col-3">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-secondary">View</button>
                                </div>
                            </div>
                            <div class="col-3"><h5>{{ date("F j, Y", strtotime($timesheet->start)) }}</h5></div>
                            <div class="col-3"><h5>{{ date("F j, Y", strtotime($timesheet->end)) }}</h5></div>
                            <div class="col-3"><h5>Hours Worked</h5></div>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="tab-pane container fade" id="denied">
                @foreach($timesheets as $timesheet)
                    @if($timesheet->status->last()->name == 'denied')
                        <div class="row mt-2">
                            <div class="col-3">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-secondary">View</button>
                                </div>
                            </div>
                            <div class="col-3"><h5>{{ date("F j, Y", strtotime($timesheet->start)) }}</h5></div>
                            <div class="col-3"><h5>{{ date("F j, Y", strtotime($timesheet->end)) }}</h5></div>
                            <div class="col-3"><h5>Hours Worked</h5></div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>


@endsection

@section('footer_scripts')
@endsection
