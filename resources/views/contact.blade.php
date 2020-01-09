@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{ trans('texts.contact_us') }}</div>
                <div class="panel-body">
                    {!! Form::open(['url' => route('contact.send')]) !!}
                        <div class="form-group {!! $errors->has('subject') ? 'has-error' : '' !!}">
                            {!! Form::label('Sujet', trans('texts.subject_request')) !!}
                            {!! Form::text('subject', null, ['class' => 'form-control']) !!}
                            {!! $errors->first('subject', '<small class="help-block">:message</small>') !!}
                        </div>
                        <div class="form-group {!! $errors->has('email') ? 'has-error' : '' !!}">
                            {!! Form::label('E-mail', trans('texts.your_email')) !!}
                            {!! Form::email('email', Auth::check() ? Auth::user()->email : null, ['class' => 'form-control']) !!}
                            {!! $errors->first('email', '<small class="help-block">:message</small>') !!}
                        </div>
                        <div class="form-group {!! $errors->has('description') ? 'has-error' : '' !!}">
                            {!! Form::label('description', trans('texts.explanation')) !!}
                            {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => '10']) !!}
                            {!! $errors->first('description', '<small class="help-block">:message</small>') !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit(trans('texts.send'), ['class' => 'btn btn-info pull-right']) !!}
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
