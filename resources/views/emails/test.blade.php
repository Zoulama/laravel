@extends('emails.master_user')

@section('body')
    <h2>{{ trans('texts.ok') }}</h2>
    <p>
        {{ trans('texts.ok') }}
    </p>

    <p>
        {{ trans('texts.no') }}
    </p>

    <p>
        {{ trans('texts.no') }}
    </p>
    &nbsp;
    <div>
        <center>
           khoulio djim
        </center>
    </div>
    &nbsp;<p>
        {{ trans('texts.export') }}
    </p>
    <div>
        <p>{{ trans('texts.app_name') }}</p>
        <p>{{ trans('texts.app_name') }}</p>
    </div>
@stop
