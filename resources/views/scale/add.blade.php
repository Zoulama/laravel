@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{ trans('texts.add_new_scale') }}</div> 
                <div class="panel-body">
                {!! Form::open(['url' => route('scales.link')]) !!}
                    {!! Form::label('alias', trans('texts.name')) !!}
                    <div class="form-group {!! $errors->has('alias') ? 'has-error' : '' !!}">
                        {!! Form::text('alias', null, ['class' => 'form-control', 'placeholder' => trans('texts.scale_name_comment')]) !!}
                        {!! $errors->first('alias', '<small class="help-block">:message</small>') !!}
                    </div>
                    {!! Form::label('reference', trans('texts.scale_reference')) !!}
                    <div class="form-group {!! $errors->has('reference') ? 'has-error' : '' !!}">
                        {!! Form::text('reference', null, ['class' => 'form-control', 'placeholder' => trans('texts.example')]) !!}
                        {!! $errors->first('reference', '<small class="help-block">:message</small>') !!}
                    </div>
                    {!! Form::submit(trans('texts.add'), ['class' => 'btn btn-info pull-right']) !!}
                {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection