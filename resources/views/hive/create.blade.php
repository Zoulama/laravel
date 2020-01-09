@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                {!! Form::open(['url' => route('hives.store')]) !!}
                <div class="row">
                    <div class="form-group col-sm-12 {!! $errors->has('alias') ? 'has-error' : '' !!}">
                        {!! Form::text('alias', null, ['class' => 'form-control', 'placeholder' => trans('texts.hive_customer_name')]) !!}. 
                        {!! $errors->first('alias', '<small class="help-block">:message</small>') !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-8 {!! $errors->has('imei') ? 'has-error' : '' !!}">
                        {!! Form::text('imei', null, ['class' => 'form-control', 'placeholder' => trans('texts.hive_imei')]) !!}  
                        {!! $errors->first('imei', '<small class="help-block">:message</small>') !!}
                    </div>
                    <div class="form-group col-sm-4 {!! $errors->has('installed_at') ? 'has-error' : '' !!}">
                        {!! Form::text('installed_at', null, ['class' => 'form-control', 'placeholder' => trans('texts.installation_date')]) !!}  
                        {!! $errors->first('installed_at', '<small class="help-block">:message</small>') !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4 {!! $errors->has('latitude') ? 'has-error' : '' !!}">
                        {!! Form::text('latitude', null, ['class' => 'form-control', 'placeholder' => trans('texts.latitude')]) !!}  
                        {!! $errors->first('latitude', '<small class="help-block">:message</small>') !!}
                    </div>
                    <div class="form-group col-sm-4 {!! $errors->has('longitude') ? 'has-error' : '' !!}">
                        {!! Form::text('longitude', null, ['class' => 'form-control', 'placeholder' => trans('texts.longitude')]) !!}  
                        {!! $errors->first('longitude', '<small class="help-block">:message</small>') !!}
                    </div>
                    <div class="form-group col-sm-4 {!! $errors->has('altitude') ? 'has-error' : '' !!}">
                        {!! Form::text('altitude', null, ['class' => 'form-control', 'placeholder' => trans('texts.altitude')]) !!}  
                        {!! $errors->first('altitude', '<small class="help-block">:message</small>') !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-6 {!! $errors->has('phone_number') ? 'has-error' : '' !!}">
                        {!! Form::text('phone_number', null, ['class' => 'form-control', 'placeholder' => trans('texts.phone')]) !!}  
                        {!! $errors->first('phone_number', '<small class="help-block">:message</small>') !!}
                    </div>
                    <div class="form-group col-sm-3 {!! $errors->has('pin_code') ? 'has-error' : '' !!}">
                        {!! Form::text('pin_code', null, ['class' => 'form-control', 'placeholder' => trans('texts.pin_code')]) !!}  
                        {!! $errors->first('pin_code', '<small class="help-block">:message</small>') !!}
                    </div>
                    <div class="form-group col-sm-3 {!! $errors->has('puk_code') ? 'has-error' : '' !!}">
                        {!! Form::text('puk_code', null, ['class' => 'form-control', 'placeholder' => trans('texts.puk_code')]) !!}  
                        {!! $errors->first('puk_code', '<small class="help-block">:message</small>') !!}  
                    </div>
                </div>
                <div class="form-group {!! $errors->has('comment') ? 'has-error' : '' !!}">
                    {!! Form::textarea ('comment', null, ['class' => 'form-control', 'placeholder' => trans('texts.comment_info')]) !!} 
                    {!! $errors->first('comment', '<small class="help-block">:message</small>') !!}
                </div>
                {!! Form::submit('Créer la nouvelle ruche', ['class' => 'btn btn-info pull-right']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
