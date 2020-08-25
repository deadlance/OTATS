@extends('layouts.app')

@section('template_title')
    {{ Auth::user()->name }}'s' Timesheets
@endsection

@section('template_fastload_css')
@endsection

@section('content')
    <div class="container">

        <h3>Employee to Manager Assignment</h3>
        <hr />

        <!--
        <pre>
            {{ json_encode($users, JSON_PRETTY_PRINT) }}
        </pre>
        -->

        <!--
        <h6>Employee Manager Object</h6>
        <pre>
            {{ json_encode($employeemanager, JSON_PRETTY_PRINT) }}
        </pre>
        -->

        @foreach($users as $manager)
            @if($manager->hasRole(['manager']))

                <form action="{{ route('employeemanager.store') }}" method="POST">
                    <div class="card mb-4">
                        <div class="card-header bg-primary"><h3>{{ $manager->first_name }} {{ $manager->last_name }}</h3></div>
                        <div class="card-body">

                            @csrf
                            <input type="hidden" name="manager_user_id" value="{{ $manager->id }}" />

                            @foreach($users as $employee)
                                @if($employee->hasRole(['user']))
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-12">
                                                <label class="form-check-label">

                                                    @php
                                                      $set = 0;
                                                    @endphp

                                                    @foreach($employeemanager as $em)
                                                        @if($em->employee_user_id == $employee->id && $em->manager_user_id == $manager->id)
                                                            @php
                                                                $set = 1;
                                                            @endphp
                                                        @endif
                                                    @endforeach

                                                    @if($set)
                                                        <input type="checkbox" class="form-check-input" name="employee_user_id[]" value="{{ $employee->id }}" checked>{{ $employee->first_name }} {{ $employee->last_name }}
                                                    @else
                                                        <input type="checkbox" class="form-check-input" name="employee_user_id[]" value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}
                                                    @endif


                                                    @php
                                                        $set = 0;
                                                    @endphp

                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                        </div>
                        <div class="card-footer"><button type="submit" class="btn btn-block btn-outline-primary" onClick="console.log('clicked\n')">Update {{ $manager->first_name }} {{ $manager->last_name }}'s List</button></div>
                    </div>
                </form>


            @endif
        @endforeach






    </div>
@endsection

@section('footer_scripts')
@endsection
