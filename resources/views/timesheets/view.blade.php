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
                <div class="btn-group">
                    <button type="button" class="btn btn-primary" id="comments_button" data-toggle="modal" data-target="#commentsModal">Comments
                        @if(isset($timesheet_data['comments']))
                            <span class="badge badge-dark">{{ count($timesheet_data['comments']) }}</span>
                        @endif
                    </button>
                    <button type="button" class="btn btn-info" id="PTO" data-toggle="modal" data-target="#PTOModal">PTO Hours</button>
                </div>
                    <div class="btn-group">

                    @role('manager')
                        <form action="/timesheet/return/{{ $timesheet_data['id'] }}" method="get">
                            @csrf
                            <input type="hidden" name="timesheet_id" value="{{ $timesheet_data['id'] }}">
                            <button type="submit" class="btn btn-warning">Return</button>
                        </form>

                        <form action="/timesheet/approve/{{ $timesheet_data['id'] }}" method="get">
                            @csrf
                            <input type="hidden" name="timesheet_id" value="{{ $timesheet_data['id'] }}">
                            <button type="submit" class="btn btn-success">Approve</button>
                        </form>

                        <form action="/timesheet/deny/{{ $timesheet_data['id'] }}" method="get">
                            @csrf
                            <input type="hidden" name="timesheet_id" value="{{ $timesheet_data['id'] }}">
                            <button type="submit" onClick="return confirm('Are you sure you wish to deny this timesheet?')" class="btn btn-danger">Deny</button>
                        </form>
                    @endrole
                </div>

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

                        @role('manager')
                            <div class="container mt-4">
                                <form action="/timesheet/add_comment/{{ $timesheet_data['id'] }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="timesheet_id" value="{{ $timesheet_data['id'] }}"/>
                                    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}"/>
                                    <input type="hidden" name="redirect_to" value="/timesheet/{{ $timesheet_data['id'] }}/edit"/>
                                    <div class="row">
                                        <div class="col-8">
                                            <h4>Add Comment</h4>
                                        </div>
                                        <div class="col-4">
                                            <button type="submit" class="btn btn-block btn-success">Add Comment</button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <textarea class="form-control" rows="5" id="comment"
                                                          name="comment"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        @endrole

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

                        @role('manager')
                        <form method="post" action="/timesheet/add_hours/{{ $timesheet_data['id'] }}">
                            @csrf
                            <input type="hidden" name="timesheet_id" value="{{ $timesheet_data['id'] }}"/>
                            <input type="hidden" name="redirect_to" value="/timesheet/{{ $timesheet_data['id'] }}/edit"/>
                            <div class="container">
                                <div class="row">
                                    <div class="col-4">
                                        <select id="hours" name="hours" class="form-control">
                                            <option value="0.25">15 minutes</option>
                                            <option value="0.50">30 minutes</option>
                                            <option value="0.75">45 minutes</option>
                                            <option value="1.00">1 hour</option>
                                            <option value="1.25">1 hour 15 minutes</option>
                                            <option value="1.50">1 hour 30 minutes</option>
                                            <option value="1.75">1 hour 45 minutes</option>
                                            <option value="2.00">2 hours</option>
                                            <option value="2.25">2 hours 15 minutes</option>
                                            <option value="2.50">2 hours 30 minutes</option>
                                            <option value="2.75">2 hours 45 minutes</option>
                                            <option value="3.00">3 hours</option>
                                            <option value="3.25">3 hours 15 minutes</option>
                                            <option value="3.50">3 hours 30 minutes</option>
                                            <option value="3.75">3 hours 45 minutes</option>
                                            <option value="4.00">4 hours</option>
                                            <option value="4.25">4 hours 15 minutes</option>
                                            <option value="4.50">4 hours 30 minutes</option>
                                            <option value="4.75">4 hours 45 minutes</option>
                                            <option value="5.00">5 hours</option>
                                            <option value="5.25">5 hours 15 minutes</option>
                                            <option value="5.50">5 hours 30 minutes</option>
                                            <option value="5.75">5 hours 45 minutes</option>
                                            <option value="6.00">6 hours</option>
                                            <option value="6.25">6 hours 15 minutes</option>
                                            <option value="6.50">6 hours 30 minutes</option>
                                            <option value="6.75">6 hours 45 minutes</option>
                                            <option value="7.00">7 hours</option>
                                            <option value="7.25">7 hours 15 minutes</option>
                                            <option value="7.50">7 hours 30 minutes</option>
                                            <option value="7.75">7 hours 45 minutes</option>
                                            <option value="8.00">8 hours</option>
                                            <option value="8.25">8 hours 15 minutes</option>
                                            <option value="8.50">8 hours 30 minutes</option>
                                            <option value="8.75">8 hours 45 minutes</option>
                                            <option value="9.00">9 hours</option>
                                            <option value="9.25">9 hours 15 minutes</option>
                                            <option value="9.50">9 hours 30 minutes</option>
                                            <option value="9.75">9 hours 45 minutes</option>
                                            <option value="10.00">10 hours</option>
                                            <option value="10.25">10 hours 15 minutes</option>
                                            <option value="10.50">10 hours 30 minutes</option>
                                            <option value="10.75">10 hours 45 minutes</option>
                                            <option value="11.00">11 hours</option>
                                            <option value="11.25">11 hours 15 minutes</option>
                                            <option value="11.50">11 hours 30 minutes</option>
                                            <option value="11.75">11 hours 45 minutes</option>
                                            <option value="12.00">12 hours</option>
                                            <option value="12.25">12 hours 15 minutes</option>
                                            <option value="12.50">12 hours 30 minutes</option>
                                            <option value="12.75">12 hours 45 minutes</option>
                                            <option value="13.00">13 hours</option>
                                            <option value="13.25">13 hours 15 minutes</option>
                                            <option value="13.50">13 hours 30 minutes</option>
                                            <option value="13.75">13 hours 45 minutes</option>
                                            <option value="14.00">14 hours</option>
                                            <option value="14.25">14 hours 15 minutes</option>
                                            <option value="14.50">14 hours 30 minutes</option>
                                            <option value="14.75">14 hours 45 minutes</option>
                                            <option value="15.00">15 hours</option>
                                            <option value="15.25">15 hours 15 minutes</option>
                                            <option value="15.50">15 hours 30 minutes</option>
                                            <option value="15.75">15 hours 45 minutes</option>
                                            <option value="16.00">16 hours</option>
                                            <option value="16.25">16 hours 15 minutes</option>
                                            <option value="16.50">16 hours 30 minutes</option>
                                            <option value="16.75">16 hours 45 minutes</option>
                                            <option value="17.00">17 hours</option>
                                            <option value="17.25">17 hours 15 minutes</option>
                                            <option value="17.50">17 hours 30 minutes</option>
                                            <option value="17.75">17 hours 45 minutes</option>
                                            <option value="18.00">18 hours</option>
                                            <option value="18.25">18 hours 15 minutes</option>
                                            <option value="18.50">18 hours 30 minutes</option>
                                            <option value="18.75">18 hours 45 minutes</option>
                                            <option value="19.00">19 hours</option>
                                            <option value="19.25">19 hours 15 minutes</option>
                                            <option value="19.50">19 hours 30 minutes</option>
                                            <option value="19.75">19 hours 45 minutes</option>
                                            <option value="20.00">20 hours</option>
                                            <option value="20.25">20 hours 15 minutes</option>
                                            <option value="20.50">20 hours 30 minutes</option>
                                            <option value="20.75">20 hours 45 minutes</option>
                                            <option value="21.00">21 hours</option>
                                            <option value="21.25">21 hours 15 minutes</option>
                                            <option value="21.50">21 hours 30 minutes</option>
                                            <option value="21.75">21 hours 45 minutes</option>
                                            <option value="22.00">22 hours</option>
                                            <option value="22.25">22 hours 15 minutes</option>
                                            <option value="22.50">22 hours 30 minutes</option>
                                            <option value="22.75">22 hours 45 minutes</option>
                                            <option value="23.00">23 hours</option>
                                            <option value="23.25">23 hours 15 minutes</option>
                                            <option value="23.50">23 hours 30 minutes</option>
                                            <option value="23.75">23 hours 45 minutes</option>
                                            <option value="24.00">24 hours</option>
                                            <option value="24.25">24 hours 15 minutes</option>
                                            <option value="24.50">24 hours 30 minutes</option>
                                            <option value="24.75">24 hours 45 minutes</option>
                                            <option value="25.00">25 hours</option>
                                            <option value="25.25">25 hours 15 minutes</option>
                                            <option value="25.50">25 hours 30 minutes</option>
                                            <option value="25.75">25 hours 45 minutes</option>
                                            <option value="26.00">26 hours</option>
                                            <option value="26.25">26 hours 15 minutes</option>
                                            <option value="26.50">26 hours 30 minutes</option>
                                            <option value="26.75">26 hours 45 minutes</option>
                                            <option value="27.00">27 hours</option>
                                            <option value="27.25">27 hours 15 minutes</option>
                                            <option value="27.50">27 hours 30 minutes</option>
                                            <option value="27.75">27 hours 45 minutes</option>
                                            <option value="28.00">28 hours</option>
                                            <option value="28.25">28 hours 15 minutes</option>
                                            <option value="28.50">28 hours 30 minutes</option>
                                            <option value="28.75">28 hours 45 minutes</option>
                                            <option value="29.00">29 hours</option>
                                            <option value="29.25">29 hours 15 minutes</option>
                                            <option value="29.50">29 hours 30 minutes</option>
                                            <option value="29.75">29 hours 45 minutes</option>
                                            <option value="30.00">30 hours</option>
                                            <option value="30.25">30 hours 15 minutes</option>
                                            <option value="30.50">30 hours 30 minutes</option>
                                            <option value="30.75">30 hours 45 minutes</option>
                                            <option value="31.00">31 hours</option>
                                            <option value="31.25">31 hours 15 minutes</option>
                                            <option value="31.50">31 hours 30 minutes</option>
                                            <option value="31.75">31 hours 45 minutes</option>
                                            <option value="32.00">32 hours</option>
                                            <option value="32.25">32 hours 15 minutes</option>
                                            <option value="32.50">32 hours 30 minutes</option>
                                            <option value="32.75">32 hours 45 minutes</option>
                                            <option value="33.00">33 hours</option>
                                            <option value="33.25">33 hours 15 minutes</option>
                                            <option value="33.50">33 hours 30 minutes</option>
                                            <option value="33.75">33 hours 45 minutes</option>
                                            <option value="34.00">34 hours</option>
                                            <option value="34.25">34 hours 15 minutes</option>
                                            <option value="34.50">34 hours 30 minutes</option>
                                            <option value="34.75">34 hours 45 minutes</option>
                                            <option value="35.00">35 hours</option>
                                            <option value="35.25">35 hours 15 minutes</option>
                                            <option value="35.50">35 hours 30 minutes</option>
                                            <option value="35.75">35 hours 45 minutes</option>
                                            <option value="36.00">36 hours</option>
                                            <option value="36.25">36 hours 15 minutes</option>
                                            <option value="36.50">36 hours 30 minutes</option>
                                            <option value="36.75">36 hours 45 minutes</option>
                                            <option value="37.00">37 hours</option>
                                            <option value="37.25">37 hours 15 minutes</option>
                                            <option value="37.50">37 hours 30 minutes</option>
                                            <option value="37.75">37 hours 45 minutes</option>
                                            <option value="38.00">38 hours</option>
                                            <option value="38.25">38 hours 15 minutes</option>
                                            <option value="38.50">38 hours 30 minutes</option>
                                            <option value="38.75">38 hours 45 minutes</option>
                                            <option value="39.00">39 hours</option>
                                            <option value="39.25">39 hours 15 minutes</option>
                                            <option value="39.50">39 hours 30 minutes</option>
                                            <option value="39.75">39 hours 45 minutes</option>
                                            <option value="40.00">40 hours</option>
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        {!! Form::select('hourtype', $hourtypes, null, ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="col-4">
                                        <input type="text" class="form-control" id="notes" name="notes" placeholder="notes" />
                                    </div>
                                    <div class="col-2">
                                        <button type="submit" id="addhours" class="btn btn-block btn-success">Add PTO</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        @endrole
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
