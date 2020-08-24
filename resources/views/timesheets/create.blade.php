@extends('layouts.app')

@section('template_title')
    {{ Auth::user()->name }}'s' Timesheets
@endsection

@section('template_fastload_css')
@endsection

@section('content')
<div class="container">

    <h3>Create Timesheet</h3>

    <form action="{{ route('timesheet.store') }}" method="POST">

        @csrf

        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}" />

    <div class="row">
        <div class="col-4">
            Start Date: <input id="start" name="start" width="276" />
        </div>
        <div class="col-4">
            End Date: <input id="end" name="end" width="276" />
        </div>
    </div>
    <div class="row">
        <div class="col-12 mt-2">
            <button type="submit" class="btn btn-primary">Create</button>
        </div>
    </div>

    </form>
</div>
@endsection

@section('footer_scripts')
    <script>
        var today = new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());
        $('#start').datepicker({
            uiLibrary: 'bootstrap4',
            iconsLibrary: 'fontawesome',
            format: 'yyyy-mm-dd',
            maxDate: function () {
                return $('#end').val();
            }
        });
        $('#end').datepicker({
            uiLibrary: 'bootstrap4',
            iconsLibrary: 'fontawesome',
            format: 'yyyy-mm-dd',
            minDate: function () {
                return $('#start').val();
            }
        });
    </script>
@endsection
