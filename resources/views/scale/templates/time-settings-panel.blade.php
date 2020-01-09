<?php
use \Carbon\Carbon;
?>

<div class="panel panel-default">
	<div class="panel-heading">{{ trans('texts.setting') }}</div>
	<div class="panel-body">
		<div class="col-sm-12">
			<div class="form-group">
				{!! Form::label('date_range', trans('texts.period')) !!}
				{!! Form::text('date_range', null, ['class' => 'form-control']) !!}
				{!! Form::hidden('from_date', Carbon::now()->subWeek()->format('Y-m-d H:i:s'), ['class' => 'form-control']) !!}
				{!! Form::hidden('to_date', Carbon::now()->format('Y-m-d H:i:s'), ['class' => 'form-control']) !!}
			</div>
		</div>
	</div>
</div>