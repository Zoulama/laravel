@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{ trans('texts.about_you') }}</div> 
                <div class="panel-body">
                    {!! Form::open(['url' => route('home.update')]) !!}
                        <div class="form-group {!! $errors->has('last_name') ? 'has-error' : '' !!}">
                            {!! Form::label('Nom', trans('texts.your_last_name')) !!}
                            {!! Form::text('last_name', Auth::user()->last_name, ['class' => 'form-control']) !!}
                            {!! $errors->first('last_name', '<small class="help-block">:message</small>') !!}
                        </div>
                        <div class="form-group {!! $errors->has('first_name') ? 'has-error' : '' !!}">
                            {!! Form::label('Prénom', trans('texts.your_first_name')) !!}
                            {!! Form::text('first_name', Auth::user()->first_name, ['class' => 'form-control']) !!}
                            {!! $errors->first('first_name', '<small class="help-block">:message</small>') !!}
                        </div>
                        <div class="form-group {!! $errors->has('email') ? 'has-error' : '' !!}">
                            {!! Form::label('E-mail', trans('texts.your_email')) !!}
                            {!! Form::email('email', Auth::user()->email, ['class' => 'form-control']) !!}
                            {!! $errors->first('email', '<small class="help-block">:message</small>') !!}
                        </div>
                        <div class="form-group {!! $errors->has('phone_number') ? 'has-error' : '' !!}">
                            {!! Form::label('Numéro de téléphone', trans('texts.your_phone_number')) !!}
                            {!! Form::text('phone_number', Auth::user()->phone_number, ['class' => 'form-control']) !!}
                            {!! $errors->first('phone_number', '<small class="help-block">:message</small>') !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit(trans('texts.update'), ['class' => 'btn btn-info pull-right']) !!}
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
