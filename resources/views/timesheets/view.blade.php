@extends('layouts.app')

@section('template_title')
    {{ Auth::user()->name }}'s' Timesheet
@endsection

@section('template_fastload_css')
@endsection

@section('content')
    @php
        $submit = false;
    @endphp

    <div class="container">

        <div class="row">
            <div class="col-6">
                <h3>View Timesheet</h3>
            </div>
            <div class="col-6 text-right">
                <form action="/timesheet/submit/{{ $timesheet_data['id'] }}" method="get">
                    @csrf
                    <input type="hidden" name="timesheet_id" value="{{ $timesheet_data['id'] }}">

                    <button type="button" class="btn btn-warning" id="comments_button" data-toggle="modal" data-target="#commentsModal">Comments
                        @if(isset($timesheet_data['comments']))
                            <span class="badge badge-dark">{{ count($timesheet_data['comments']) }}</span>
                        @endif
                    </button>
                    <button type="button" class="btn btn-info" id="PTO" data-toggle="modal" data-target="#PTOModal">PTO Hours</button>
                </form>

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
                    Time Worked: {{ intdiv($timesheet_data['total_minutes'], 60) }} hours
                    and {{ (int)$timesheet_data['total_minutes'] % 60 }} minutes
                </div>
                <div class="col-12 text-center">
                    PTO: {{ intdiv($timesheet_data['total_pto_time'], 60) }} hour(s) and {{ $timesheet_data['total_pto_time'] % 60 }} minutes
                </div>
            </div>
        </div>

        @foreach($timesheet_data['dailies'] as $day)

            <div class="jumbotron">
                <div class="row">
                    <div class="col-lg-4 col-md-12 text-center">
                        <h4>{{ date("D, M j, Y", strtotime($day['date'])) }}</h4>

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

                                </div>
                            </div>

                        @endforeach

                        @if($counter % 2 != 0 && $counter > 0)
                            @php
                                $submit = false;
                            @endphp

                            <div class="row">
                                <div class="col-12 bg-warning pt-2 pb-2">
                                    You will not be able to submit this timesheet due to having an incorrect number of
                                    activities. You must clock-out for every clock-in.
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

    @endforeach

    <!-- Comments Modal -->
        <div class="modal" id="commentsModal">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Comments</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <div class="container">
                            @if(isset($timesheet_data['comments']))
                                @foreach($timesheet_data['comments'] as $comment)
                                    <div class="row border mt-2">
                                        <div class="col-8 bg-light">
                                            {{ $comment->user->first_name }} {{ $comment->user->last_name }}
                                        </div>
                                        <div class="col-4 bg-light">
                                            {{ $comment->created_at }}
                                        </div>
                                        <div class="col-12">
                                            {{ $comment->comment }}
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>


        <!-- PTO Modal -->
        <div class="modal" id="PTOModal">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">PTO Hours</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-12 mt-4">
                                    Total PTO Used: {{ $timesheet_data['total_pto'] }} hours.
                                </div>
                            </div>
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Hours</th>
                                    <th>Type</th>
                                    <th>Notes</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($hours as $h)
                                    <tr>
                                        <td>{{ $h->hours }}</td>
                                        <td>{{ $h->hourtype_name }}</td>
                                        <td>{{ $h->notes }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>



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
            var diff = (dt2.getTime() - dt1.getTime()) / 1000;
            diff /= 60;
            return Math.abs(Math.round(diff));
        }

    </script>
@endsection
