
<div id="weatherPlugin" class="panel panel-default">

@if ($hive->isGeolocated())

	<div class="panel-heading"><img height="20" id="wIcon" src=""> {{ trans('texts.current_weather') }}</div>
	<div class="panel-body">
		<table id="weatherTable" class="table-compressed table-striped col-sm-12" style="display: none;">
			<thead>
				<th width="50%"></th>
				<th width="50%"></th>
			</thead>
			<tbody>
				<tr>
					<td>
						{{ trans('texts.temperature') }}
					</td>
					<td>
						<span id="wTempC"></span>&deg;C (ressentie <span id="wFeelsLikeC"></span>&deg;C)
					</td>
				</tr>
				<tr>
					<td>
						{{ trans('texts.wind') }}
					</td>
					<td>
						<span id="wWindKPH"></span> km/h{{--  (direction <span id="wWindDir"></span>) --}}
					</td>
				</tr>
				<tr>
					<td>
						{{ trans('texts.pressure') }}
					</td>
					<td>
						<span id="wPressureMB"></span> mbar
					</td>
				</tr>
				<tr>
					<td>
						{{ trans('texts.humidity') }}
					</td>
					<td>
						<span id="wHumidity"></span>%
					</td>
				</tr>
				<tr>
					<td>
						{{ trans('texts.precipitation') }}
					</td>
					<td>
						<span id="wPrecipMM"></span> ml
					</td>
				</tr>
				<tr>
					<td>
						{{ trans('texts.cloud_cover') }}
					</td>
					<td>
						<span id="wCloud"></span>%
					</td>
				</tr>
			</tbody>
		</table>
	</div>

@else

	<div class="panel-heading">{{ trans('texts.weather_unavailable') }}</div>
	<div class="panel-body">
		{{ trans('texts.must_located_hive') }}
	</div>

@endif

</div>