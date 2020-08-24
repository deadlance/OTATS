@extends('layouts.app')

@section('template_title')
    {{ Auth::user()->name }}'s' Timesheets
@endsection

@section('template_fastload_css')
@endsection

@section('content')
    @php
        $submit = false;
    @endphp

<!--
        <pre>
            {{ json_encode($timesheet_data, JSON_PRETTY_PRINT) }}
        </pre>

-->


    <div class="container">

        <div class="row">
            <div class="col-6">
                <h3>Edit Timesheet</h3>
            </div>
            <div class="col-6">

                @if($timesheet_data['submittable'])
                    <button class="btn btn-block btn-primary" id="submit_timesheet">Submit Timesheet</button>
                @else
                    <button class="btn btn-block btn-primary" id="submit_timesheet" disabled>Submit Timesheet</button>
                @endif

            </div>
        </div>

        <div class="jumbotron">
            <div class="row">
                <div class="col-6 text-center">
                    <h4>Start Date</h4>{{ date("M j, Y", strtotime($timesheet_data['start_date'])) }}
                </div>
                <div class="col-6 text-center">
                    <h4>End Date</h4>{{ date("M j, Y", strtotime($timesheet_data['last_date'])) }}
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    Total Time: {{ intdiv($timesheet_data['total_minutes'], 60) }} hours and {{ (int)$timesheet_data['total_minutes'] % 60 }} minutes
                </div>
            </div>
        </div>

        @foreach($timesheet_data['dailies'] as $day)

            <div class="jumbotron">
                <div class="row">
                    <div class="col-lg-4 col-md-12 text-center">
                        <h4>{{ date("D, M j, Y", strtotime($day['date'])) }}</h4>
                        <button class="btn btn-block btn-primary" data-toggle="modal" data-target="#modal_{{ $day['date'] }}" data-backdrop="static">Add Entry</button>
                        <br />
                        {{ intdiv($day['minutes'], 60) }} hours and {{ (int)$day['minutes'] % 60 }} minutes
                    </div>
                    <div class="col-lg-8 col-md-12">

                        @php
                            $counter = count($day['entries']);
                            $submit = false;
                        @endphp

                        @foreach($day['entries'] as $entry)

                            <div class="row bg-light mt-1 mb-1">
                                <div class="d-inline col-2 pt-1 pb-1 align-middle">
                                    {{ date("H:i", strtotime($entry->activity)) }}
                                </div>
                                <div class="d-inline col-8 pt-1 pb-1 align-middle">
                                    {{ $entry['comments'] }}
                                </div>
                                <div class="d-inline col-2 pt-1 pb-1 text-right align-middle">
                                    <form action="{{ route('entry.destroy',$entry->id) }}" method="POST">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-link" style="color:tomato" onClick="return confirm('Are you sure you wish to delete this entry?')"><span class="fa fa-times"></span></button>
                                    </form>
                                </div>
                            </div>

                        @endforeach

                        @if($counter % 2 != 0 && $counter > 0)
                            @php
                                $submit = false;
                            @endphp

                            <div class="row">
                                <div class="col-12 bg-warning pt-2 pb-2">
                                    You will not be able to submit this timesheet due to having an incorrect number of activities. You must clock-out for every clock-in.
                                </div>
                            </div>

                        @elseif($counter % 2 == 0 && $counter > 0)
                            @php
                                // Even number of entries
                                $submit = true;
                            @endphp
                        @else
                            @php
                                // Even number of entries
                                $submit = true;
                            @endphp
                        @endif

                    </div>
                </div>
            </div>

            <!-- The Modal -->
            <div class="modal" id="modal_{{ $day['date'] }}">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                    <div class="modal-content">
                        <form action="{{ route('entry.store') }}" method="POST">

                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h4 class="modal-title"><h2>Add Entry for: {{ date("D, M j, Y", strtotime($day['date'])) }}</h2></h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>

                            <!-- Modal body -->
                            <div class="modal-body">
                                @csrf
                                <input type="hidden" name="timesheet_id" value="{{ $timesheet_data['id'] }}" />
                                <input type="hidden" name="entry_date" value="{{ $day['date'] }}" />

                                <div class="row">
                                    <div class="col-6">
                                        <h4>Enter Time</h4>
                                    </div>
                                    <div class="col-6">
                                        <input id="clock_in{{ $day['date'] }}" name="activity" width="276" />
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <h4>Comments</h4>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="comment">Comments:</label>
                                            <textarea class="form-control" rows="5" id="comments" name="comments" maxlength="4294967295"></textarea>
                                        </div>
                                    </div>
                                </div>


                            </div>

                            <!-- Modal footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal" onClick="resetTimes('clock_in{{ $day['date'] }}','clock_out{{ $day['date'] }}')">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add Entry</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

        @endforeach


    </div>
@endsection

@section('footer_scripts')
    <script>
        var clockIn = '';
        var clockOut = '';

        $("[id^=clock_in]").timepicker();
        $("[id^=clock_out]").timepicker();

        /* This isn't working properly
        $("[id^=clock_out]").change(function(){
            if(clockOut.val() <= clockIn.val()) {
                alert('You must clock out AFTER you clock in.');
            }
        });

         */

        function setInputs(clock_in, clock_out) {
            clockIn = $('#' + clock_in);
            clockOut = $('#' + clock_out);
        }

        function resetTimes(clock_in, clock_out) {
            $("#" + clock_in).val('');
            $("#" + clock_out).val('');
        }

        function calculateWorkedTime(work_day, clock_in, clock_out) {
            dt1 = new Date(work_day + ' ' + $('#' + clock_in).val());
            dt2 = new Date(work_day + ' ' + $('#' + clock_out).val());
            $('#time_worked').val(diff_minutes(dt1, dt2));
            //console.log(diff_minutes(dt1, dt2));
        }

        function diff_minutes(dt2, dt1) {
            var diff =(dt2.getTime() - dt1.getTime()) / 1000;
            diff /= 60;
            return Math.abs(Math.round(diff));
        }

    </script>
@endsection
