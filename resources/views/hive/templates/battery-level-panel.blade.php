<?php
list($bLoading, $bLevel) = $hive->getCurrentBatteryState();
?>
<div class="panel panel-default">
@if ( ! is_null($bLevel))
	<div class="panel-heading">{{ trans('texts.current_battery_status') }} {{ $bLoading ?  trans('texts.loading_to') : "" }}</div>
	<table width="100%"><tr><td align="center">
		<div class="panel-body" id="batteryLevelGauge"></div>
	</td></tr></table>
@else
	<div class="panel-heading">{{ trans('texts.current_battery_status')</div>
	<div class="panel-body">
		{{ trans('texts.no_data_concerned_battery') }}
	</div>
@endif
</div>