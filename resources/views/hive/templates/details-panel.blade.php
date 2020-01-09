<?php
use \Carbon\Carbon;
?>

<div class="panel panel-default">
	<div class="panel-heading">{{ trans('texts.details') }}</div>
	<div class="panel-body">
	{!! Form::open(['url' => route('hives.update')]) !!}
		<div class="form-group">
			{!! Form::label('reference', trans('texts.hive_reference')) !!}
			{!! Form::text('reference', $hive->reference, ['class' => 'form-control', 'disabled']) !!}
		</div>
		<div class="form-group {!! $errors->has('alias') ? 'has-error' : '' !!}">
			{!! Form::label('alias', trans('texts.name')) !!}
			{!! Form::text('alias', $hive->alias, ['class' => 'form-control']) !!}
			{!! $errors->first('alias', '<small class="help-block">:message</small>') !!}
		</div>
		<div class="form-group {!! $errors->has('installed_at') ? 'has-error' : '' !!}">
			{!! Form::label('installed_at', trans('texts.installed_on')) !!}
			{!! Form::text('installed_at', Carbon::parse($hive->installed_at)->format('d/m/Y'), ['class' => 'form-control']) !!}
			{!! $errors->first('installed_at', '<small class="help-block">:message</small>') !!}
		</div>
		<div class="form-group {!! $errors->has('latitude') ? 'has-error' : '' !!}">
			{!! Form::label('latitude', trans('texts.latitude') ' :') !!}
			{!! Form::text('latitude', $hive->latitude, ['class' => 'form-control']) !!}
			{!! $errors->first('latitude', '<small class="help-block">:message</small>') !!}
		</div>
		<div class="form-group {!! $errors->has('longitude') ? 'has-error' : '' !!}">
			{!! Form::label('longitude', trans('texts.longitude') ' :') !!}
			{!! Form::text('longitude', $hive->longitude, ['class' => 'form-control']) !!}
			{!! $errors->first('longitude', '<small class="help-block">:message</small>') !!}
		</div>
		<div class="form-group {!! $errors->has('altitude') ? 'has-error' : '' !!}">
			{!! Form::label('altitude', trans('texts.altitude') ' :') !!}
			{!! Form::text('altitude', $hive->altitude, ['class' => 'form-control']) !!}
			{!! $errors->first('altitude', '<small class="help-block">:message</small>') !!}
		</div>
		<div class="form-group {!! $errors->has('comment') ? 'has-error' : '' !!}">
			{!! Form::label('comment', trans('texts.details_to')) !!}
			{!! Form::textarea('comment', $hive->comment, ['class' => 'form-control', 'rows' => '2']) !!}
			{!! $errors->first('comment', '<small class="help-block">:message</small>') !!}
		</div>
		<div class="form-group">
			{!! Form::submit(trans('texts.update'), ['class' => 'btn btn-info pull-right']) !!}
		</div>
		{!! Form::hidden('hive_id', $hive->id) !!}
	{!! Form::close() !!}
	</div>
</div>