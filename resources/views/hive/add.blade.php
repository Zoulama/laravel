@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                {!! Form::open(['url' => route('hives.link')]) !!}
                {!! Form::label('reference', trans('texts.hive_reference')) !!}
                <div class="form-group {!! $errors->has('reference') ? 'has-error' : '' !!}">
                    {!! Form::text('reference', null, ['class' => 'form-control', 'placeholder' => 'exemple : POUMOMAU-CLAVRYMEU']) !!}
                    {!! $errors->first('reference', '<small class="help-block">:message</small>') !!}
                </div>
                {!! Form::submit('Ajouter', ['class' => 'btn btn-info pull-left']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection