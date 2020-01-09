<?php
	use \Carbon\Carbon;
	use App\WeightReference;
	use App\Traits\ColorsTrait;
	use App\Traits\HelpfulTrait;

	//GOOGLE API KEY: key=AIzaSyAzWE3TFj2s2IRUk5tlw-uUMzvC7NVy_wo

	// Dates en français : En attendant une meilleure soltuion
	Carbon::setLocale('fr');
	date_default_timezone_set('Europe/Paris');
	$version = time();
	$format = 'Y-m-d H:i:s';
	$now = Carbon::now();
	$aMonthAgo = Carbon::now()->subMonth();
	$aWeekAgo = Carbon::now()->subWeek();
	$aDayAgo = Carbon::now()->subDay();
	$maxDate = Carbon::now()->format($format);
	$minDate = Carbon::now()->format($format);

	//$reports = $scale->reports()->whereBetween('at', [$aMonthAgo, $now])->get();
	$reports = $scale->reports()->get();

	// Les deux premiers sont pour l'initialisation (poids à vide, puis poids à 30kg)
	$shouldChartBeShown = (count($reports) > 0);

	// Quand affichage du calendrier
	if ($shouldChartBeShown) {
		$minDate = Carbon::parse($scale->reports()->orderBy('at', 'ASC')->first()->at)->format('Y-m-d H:i:s');
	}

	$owners = $scale->owners;
	list($batteryLoading, $batteryLevel) = $scale->getCurrentBatteryState();

	// Couleurs température
	$colors = array();
	$colors["temperature"] = ColorsTrait::hexa("sbh_dark_pink");
	$colors["weight"] = ColorsTrait::hexa("sbh_orange");
	$colors["hygrometry"] = ColorsTrait::hexa("sbh_light_cyan");
	$colors["batteryLevel"] = ColorsTrait::hexa("sbh_gray");
?>

@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="row">
			{{-- <div class="col-sm-4">
				@include('scale.templates.time-settings-panel')
				@include('scale.templates.stock-panel')
				@include('scale.templates.weather-panel')
				@include('scale.templates.battery-level-panel')
				@include('scale.templates.details-panel')
			</div>
			<div class="col-sm-8">
				@include('scale.templates.analysis-panel')
				@include('scale.templates.weight-panel')
				@include('scale.templates.temperature-panel')
				@include('scale.templates.hygrometry-panel')
				@include('scale.templates.noise-panel')
			</div> --}}

			<!-- Début Onglets -->
			<ul class="nav nav-tabs">
				@if ($shouldChartBeShown)
					<li class="active">
						<a data-toggle="tab" href="#analysis" >{{ trans('texts.analyzes') }}</a>
					</li>
				@else
					<li class="disabled">
						<a>{{ trans('texts.analyzes_initialized') }}</a>
					</li>
				@endif
				<li class="{{ $shouldChartBeShown ? '' : 'active' }}">
					<a data-toggle="tab" href="#details">{{ trans('texts.informations') }}</a>
				</li>
				<li class="disabled">
					<a data-toggle="tab" href="#subscribed">{{ trans('texts.subscriptions') }}</a>
				</li>
			</ul>
			<!-- Fin Onglets -->

			<!-- Début Partie principale -->
			<div class="tab-content">
				<div id="analysis" class="tab-pane fade {{ $shouldChartBeShown ? 'in active' : '' }}">
				@if ($shouldChartBeShown)
					<!-- Début légende supérieure Graphique -->
					<fieldset class="analyticsBlock" style="display: none;">
						<legend>Analyse</legend>
						<p>
							Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
							tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
							proident, sunt in culpa qui officia deserunt mollit anim id est laborum :
							<ul>
								<li>aaaaaaaaa aaaa aaa aaaaaaaaaaa</li>
							</ul>
						</p>
					</fieldset>

					@if ($scale->isGeolocated())
					<fieldset class="analyticsBlock">
						<legend><img height="20" id="wIcon" src="">&nbsp;<span id="weatherLegend">{{ trans('texts.weather_forecast') }}</span></legend>
						<div id="weatherPlugin">
							<table id="weatherTable" class="table-condensed">
							<thead>
								<th>{{ trans('texts.temperature') }}</th>
								<th>{{ trans('texts.wind') }}</th>
								<th>{{ trans('texts.pressure') }}</th>
								<th>{{ trans('texts.humidity') }}</th>
								<th>{{ trans('texts.precipitation') }}</th>
								<th>{{ trans('texts.cloud_cover') }}</th>
							</thead>
							<tbody><tr>
								<td><span id="wTempC"></span>&deg;C<br>{{-- (ressentie <span id="wFeelsLikeC"></span>&deg;C) --}}</td>
								<td><span id="wWindKPH"></span> km/h</td>
								<td><span id="wPressureMB"></span> mbar</td>
								<td><span id="wHumidity"></span>%</td>
								<td><span id="wPrecipMM"></span> ml</td>
								<td><span id="wCloud"></span>%</td>
							</tr></tbody>
							</table>
						</div>
					</fieldset>
					@endif
					<!-- Fin légende supérieure Graphique -->

					<!-- Début Graphique -->
					<fieldset class="analyticsBlock">
						<legend>{{ trans('texts.data_evolution') }}</legend>
						<div class="panel panel-default">
							<div class="panel-heading">
								<div class="row">
									<div class="col-sm-4">
										<input class="form-control" type="text" name="date_range">
									</div>
									<div class="col-sm-6">
										{{-- <select class="form-control" multiple id="mainChartLegend">
											<option selected value="vTemperature">{{ trans('texts.temperature') }}</option>
											<option selected value="vWeight">{{ trans('texts.weight') }}</option>
											<option selected value="vHygrometry">{{ trans('texts.hygrometry') }}</option>
											<option value="vBatteryLevel">{{ trans('texts.battery_charge') }}</option>
										</select> --}}
									</div>
									<div class="col-sm-2">
										<div class="form-control btn btn-default" id="exportButton">{{ trans('texts.export') }}</div>
										<div id="fakeExportButton" style="display: none;"></div>
									</div>
								</div>
							</div>
							<div class="panel-body">
								<div class="col-sm-9">
									<div id="mainChartLoading">
										<img src="{{ asset('icons/ajax-loader.gif') }}">
									</div>
									<div id="mainChart" style="display: none;"></div>
								</div>
								<div id="sidePart" class="col-sm-3">
									<fieldset>
										<legend>{{ trans('texts.legend') }}</legend>
										<div class="checkbox">
											<label class="col-sm-12"><input checked type="checkbox" class="showLegend" name="vTemperature"> <span style="color: {{ $colors["temperature"] }}; font-weight: bold;">&#8212;</span> {{ trans('texts.temperature') }} (°C)</label>
										</div>
										<div class="checkbox">
											<label class="col-sm-12"><input checked type="checkbox" class="showLegend" name="vWeight"> <span style="color: {{ $colors["weight"] }}; font-weight: bold;">&#8212;</span> {{ trans('texts.weight') }} (kg)</label>
										</div>
										<div class="checkbox">
											<label class="col-sm-12"><input checked type="checkbox" class="showLegend" name="vHygrometry"> <span style="color: {{ $colors["hygrometry"] }}; font-weight: bold;">&#8212;</span> {{ trans('texts.hygrometry') }} (%)</label>
										</div>
										<div class="checkbox">
											<label class="col-sm-12"><input checked type="checkbox" class="showLegend" name="vBatteryLevel"> <span style="color: {{ $colors["batteryLevel"] }}; font-weight: bold;">&#8212;</span> {{ trans('texts.battery_charge') }} (V)</label>
										</div>
									</fieldset>
									<fieldset id="gaugeChartContainer">
										<legend>{{ trans('texts.battery') }}</legend>
										<div id="gaugeChart" style="display: block;"></div>
										<div style="text-align: center;">
											@if ($batteryLoading)
											   {{ trans('texts.battery_charging') }}
											@else
												{{ trans('texts.battery_status') }}
											@endif
										</div>
									</fieldset>
									<fieldset>
										<legend>{{ trans('texts.informations') }}</legend>
										<b>{{ trans('texts.reference') }}</b>
										<p><em>{{ $scale->reference }}</em></p>
										<b>{{ trans('texts.name') }}</b>
										<p><em>{{ $scale->getAlias() }}</em></p>
										<b>{{ trans('texts.located_in') }}</b>
										<p><em>{{ is_null($scale->getPlace('locality')) ? "-" : $scale->getPlace('locality') }}</em></p>
									</fieldset>
								</div>
							</div>
						</div>
					</fieldset>
					<!-- Fin Graphique -->
				@endif
				</div>
				<!-- Fin Partie principale -->

				<!-- Début de l'onlget Informations -->
				<div id="details" class="tab-pane fade {{ $shouldChartBeShown ? '' : 'in active' }}">
					{!! Form::open(['url' => route('scales.update')]) !!}
					<fieldset class="detailsBlock">
						<legend>
						{{ trans('texts.general_informations') }}
						<small><i>
						@if (count($owners) > 0)
							({{ trans('texts.belongs_to_scale') }}
							<?php $owners = array(); ?>
							@foreach ($scale->owners as $owner)
								<?php $owners[] = $owner->getFullName(); ?>
							@endforeach
							{{ implode(", ", $owners) }})
						@else
							({{ trans('texts.scale_not_belongs_to') }})
						@endif
						</i></small>
						</legend>

						<div class="row">
							<div class="col-sm-6">
								<div class="panel panel-default">
									<!-- Panneau Gauche -->
									<div class="panel-heading">{{ trans('texts.model_of_the_hive') }}</div>
									<div class="panel-body">
										<div class="form-group" id="whichTypeWrap" style="display: none;">
											{!! Form::label('whichType',  trans('texts.type_of_hive')) !!}
											{!! Form::select('whichType', [], null, ['class' => 'form-control', 'selectpicker', 'data-none-selected-text' => trans('texts.select_an_option')]) !!}
										</div>
										<div class="form-group" id="whichModelWrap" style="display: none;">
											{!! Form::label('whichModel', trans('texts.model_of_the_hive')) !!}
											{!! Form::select('whichModel', [], null, ['class' => 'form-control', 'selectpicker', 'data-none-selected-text' => trans('texts.select_an_option')]) !!}
										</div>
										<div class="form-group" id="areBodyFramesWaxedWrap" style="display: none;">
											<label for="areBodyFramesWaxed">{{ trans('texts.are_body_frame_waxed') }}</label>
											<div class="col-sm-12" style="margin-bottom: 15px;">
												<label class="radio-inline">
													<input type="radio" name="areBodyFramesWaxed" value="1"> {{ trans('texts.yes') }}
												</label>
												<label class="radio-inline">
													<input type="radio" name="areBodyFramesWaxed" value="0"> {{ trans('texts.no') }}
												</label>
											</div>
										</div>
										<div class="form-group" id="howManySupersWrap" style="display: none;">
											{!! Form::label('howManySupers', trans('texts.number_of_increase')) !!}
											{!! Form::number('howManySupers', null, ['class' => 'form-control', 'min' => 0, 'max' => 4]) !!}
										</div>
										<div class="form-group" id="areSuperFramesWaxedWrap" style="display: none;">
											<label for="areSuperFramesWaxed">{{ trans('texts.are_increase_frame') }}</label>
											<div class="col-sm-12" style="margin-bottom: 15px;">
												<label class="radio-inline">
													<input type="radio" name="areSuperFramesWaxed" value="1">{{ trans('texts.yes') }}
												</label>
												<label class="radio-inline">
													<input type="radio" name="areSuperFramesWaxed" value="0">{{ trans('texts.no') }}
												</label>
											</div>
										</div>
										<div class="form-group" id="whichCoverTypeWrap" style="display: none;">
											<label for="whichCoverType">{{ trans('texts.what_material') }}</label>
											<div class="col-sm-12" style="margin-bottom: 15px;">
												<label class="radio-inline">
													<input type="radio" name="whichCoverType" value="wooden">{{ trans('texts.made_of_wood') }}
												</label>
												<label class="radio-inline">
													<input type="radio" name="whichCoverType" value="metal">{{ trans('texts.made_sheet_metal') }}
												</label>
											</div>
										</div>
										<div class="form-group" id="whichWoodenCoverTypeWrap" style="display: none;">
											<label for="whichWoodenCoverType">{{ trans('texts.roof_shape') }}</label>
											<div class="col-sm-12" style="margin-bottom: 15px;">
												<label class="radio-inline">
													<input type="radio" name="whichWoodenCoverType" value="flat">{{ trans('texts.dish') }}
												</label>
												<label class="radio-inline">
													<input type="radio" name="whichWoodenCoverType" value="garden">{{ trans('texts.chalet') }}
												</label>
											</div>
										</div>
										<div class="form-group" id="whichMetalCoverTypeWrap" style="display: none;">
											<label for="whichMetalCoverType">{{ trans('texts.roof_thickness') }}</label>
											<div class="col-sm-12" style="margin-bottom: 15px;">
												<label class="radio-inline">
													<input type="radio" name="whichMetalCoverType" value="80">80mm
												</label>
												<label class="radio-inline">
													<input type="radio" name="whichMetalCoverType" value="105">105mm
												</label>
											</div>
										</div>
										<div class="form-group" id="isThereAnInnerCoverWrap" style="display: none;">
											<label for="isThereAnInnerCover">{{ trans('texts.hive_frame_cover') }}</label>
											<div class="col-sm-12" style="margin-bottom: 15px;">
												<label class="radio-inline">
													<input type="radio" name="isThereAnInnerCover" value="1">{{ trans('texts.yes') }}
												</label>
												<label class="radio-inline">
													<input type="radio" name="isThereAnInnerCover" value="0">{{ trans('texts.no') }}
												</label>
											</div>
										</div>
										<div class="form-group" id="isThereABottomBoardWrap" style="display: none;">
											<label for="isThereABottomBoard">{{ trans('texts.hive_bottom') }}</label>
											<div class="col-sm-12" style="margin-bottom: 15px;">
												<label class="radio-inline">
													<input type="radio" name="isThereABottomBoard" value="1">{{ trans('texts.yes') }}
												</label>
												<label class="radio-inline">
													<input type="radio" name="isThereABottomBoard" value="0">{{ trans('texts.no') }}
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Panneau droite -->
							<div class="col-sm-6">
								<div class="panel panel-default">
									<div class="panel-heading">{{ trans('texts.detail_information') }}</div>
									<div class="panel-body">
										<div class="form-group">
											{!! Form::label('reference', trans('texts.your_scale_reference')) !!}
											{!! Form::text('reference', $scale->reference, ['class' => 'form-control', 'disabled']) !!}
										</div>
										<div class="form-group">
											{!! Form::label(null, trans('texts.empty_weight_hive')) !!}
											<p class="col-sm-12">{{ $scale->hive_weight }}kg</p>
										</div>
										<div class="form-group {!! $errors->has('alias') ? 'has-error' : '' !!}">
											{!! Form::label('alias', trans('texts.name')) !!}
											{!! Form::text('alias', $scale->alias, ['class' => 'form-control']) !!}
											{!! $errors->first('alias', '<small class="help-block">:message</small>') !!}
										</div>
										<div class="form-group {!! $errors->has('address') ? 'has-error' : '' !!}">
											{!! Form::label('address', trans('texts.address')) !!}
											{!! Form::text('address', $scale->getPlace('formatted'), ['class' => 'form-control', 'id' => 'addressField']) !!}
											{!! $errors->first('address', '<small class="help-block">:message</small>') !!}
										</div>
										<div class="form-group {!! $errors->has('installed_at') ? 'has-error' : '' !!}">
											{!! Form::label('installed_at', trans('texts.installed_on')) !!}
											{!! Form::text('installed_at', Carbon::parse($scale->installed_at)->format('d/m/Y'), ['class' => 'form-control']) !!}
											{!! $errors->first('installed_at', '<small class="help-block">:message</small>') !!}
										</div>
										<div class="form-group {!! $errors->has('comment') ? 'has-error' : '' !!}">
											{!! Form::label('comment', trans('texts.details_to')) !!}
											{!! Form::textarea('comment', $scale->comment, ['class' => 'form-control', 'rows' => '5']) !!}
											{!! $errors->first('comment', '<small class="help-block">:message</small>') !!}
										</div>

										<!-- MAIL - Envoit -->
										<!--
										<div class="form-group" id="choiceMailInput">
											<label for="mail_input">Recevoir la dernière requête par mail tous les matins ?</label>
											<div class="col-sm-12" style="margin-bottom: 15px;">
												<label class="radio-inline">
													<input type="radio" name="mail_input" value="1">Oui
												</label>
												<label class="radio-inline">
													<input type="radio" name="mail_input" value="0">Non
												</label>
											</div>
										</div>
										-->

										<!-- TARE -->
										<div class="form-group" id="isThereATareApplied">
										<label for="mail_input">{{ trans('texts.want_reset_scale') }}</label>
											<div class="col-sm-12" style="margin-bottom: 15px;">
												<label class="radio-inline">
													<input type="radio" name="isThereATare" value="1">{{ trans('texts.yes') }}
												</label>
												<label class="radio-inline">
													<input type="radio" name="isThereATare" value="0">{{ trans('texts.no') }}
												</label>
											</div>
											<div>
												{!! Form::label('', trans('texts.tare_value')); !!}
												{!! Form::text('tareShow', $scale->tare, ['class' => '', 'disabled']) !!}
											</div>

										</div>

										{!! Form::hidden('scale_id', $scale->id) !!}
										{!! Form::hidden('formatted', $scale->getPlace('formatted')) !!}
										{!! Form::hidden('place_id', $scale->getPlace('place_id')) !!}
										{!! Form::hidden('street_number', $scale->getPlace('street_number')) !!}
										{!! Form::hidden('route', $scale->getPlace('route')) !!}
										{!! Form::hidden('locality', $scale->getPlace('locality')) !!}
										{!! Form::hidden('postal_code', $scale->getPlace('postal_code')) !!}
										{!! Form::hidden('country', $scale->getPlace('country')) !!}
										{!! Form::hidden('latitude', $scale->getPlace('latitude')) !!}
										{!! Form::hidden('longitude', $scale->getPlace('longitude')) !!}
										{!! Form::hidden('altitude', $scale->getPlace('altitude')) !!}
									</div>

								</div>
							</div>
						</div>
						<div class="form-group">
							{!! Form::submit(trans('texts.update'), ['class' => 'btn btn-info pull-right']) !!}
						</div>
					</fieldset>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
	<!-- Fin de l'onglet Informations -->

	<!-- Début Panneau d'export des données -->
	<div class="modal fade" id="exportMenu">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title">{{ trans('texts.export_data_scale') }}
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</h3>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label class="control-label">{{ trans('texts.choose_format') }}</label>
						<select class="form-control selectpicker" id="formatsList">
							<!-- https://fr.wikipedia.org/wiki/Portable_Network_Graphics -->
							<option value="PNG" data-subtext="Portable Network Graphics">PNG</option>
							<!-- https://fr.wikipedia.org/wiki/JPEG -->
							<option value="JPG" data-subtext="Joint Photographic Experts Group">JPG {{ trans('texts.or_jpeg') }}</option>
							<!-- https://fr.wikipedia.org/wiki/Portable_Document_Format -->
							<option value="PDF" data-subtext="Portable Document Format">PDF</option>
							<!-- https://fr.wikipedia.org/wiki/Comma-separated_values -->
							<option value="CSV" data-subtext="Comma-Separated Values">CSV</option>
							<!-- https://fr.wikipedia.org/wiki/Comma-separated_values -->
							<option value="ALL_IN_CSV" data-subtext="{{ trans('texts.all_data_since_commissioning') }}">CSV {{ trans('texts.all_data') }}</option>
						</select>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" id="exportNow" data-can-export="true">{{ trans('texts.export') }}</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('texts.close') }}</button>
				</div>
			</div>
		</div>
	</div>
	<!-- Fin panneau export des données -->
@endsection

@section('style')
	<link rel="stylesheet" type="text/css" href="{{ asset('plugins/amcharts/3.21.5/plugins/export/export.css') }}?v={{ $version }}" />
	<style type="text/css">
	#mainChart, #mainChartLoading {
		height: 600px;
		margin: 0;
		padding: 0;
	}
	#mainChartLoading > img {
		position: relative;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		text-align: center;
	}
	#gaugeChart {
		height: 150px;
		margin: 0;
		padding: 0;
	}
	.analyticsBlock, .detailsBlock {
		margin-top: 15px;
	}
	.analyticsBlock > legend, .detailsBlock > legend {
		border-color: {{ ColorsTrait::hexa("sbh_light_green") }};
	}
	#sidePart {
		/*overflow: hidden;*/
	}
	#sidePart fieldset {
		margin-top: 10px;
		margin-bottom: 25px;
	}
	#sidePart legend {
		border-color: {{ ColorsTrait::hexa("sbh_light_green") }};
		margin-bottom: 5px;
	}
	#weatherTable {
		width: 100%;
		table-layout: fixed;
	}
	#weatherTable th {
		text-align: center;
	}
	#weatherTable td {
		text-align: center;
		vertical-align: top;
	}
	</style>
@endsection

@section('script')
<script src="{{ asset('plugins/amcharts/3.21.5/amcharts.js') }}?v={{ $version }}"></script>
<script src="{{ asset('plugins/amcharts/3.21.5/serial.js') }}?v={{ $version }}"></script>
<script src="{{ asset('plugins/amcharts/3.21.5/gauge.js') }}?v={{ $version }}"></script>
<script src="{{ asset('plugins/amcharts/3.21.5/plugins/export/export.min.js') }}?v={{ $version }}"></script>
<script src="{{ asset('plugins/amcharts/3.21.5/themes/light.js') }}?v={{ $version }}"></script>
<script src="{{ asset('plugins/amcharts/3.21.5/lang/fr.js') }}?v={{ $version }}"></script>
<script src="{{ asset('plugins/moment/2.18.1/moment.min.js') }}?v={{ $version }}"></script>
<script src="{{ asset('plugins/bootstrap-daterangepicker/2.1.25/js/daterangepicker.js') }}?v={{ $version }}"></script>

<!-- Google setup -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAzWE3TFj2s2IRUk5tlw-uUMzvC7NVy_wo&libraries=places&callback=initAutocomplete" async defer></script>
<script type="text/javascript">
var placeSearch,
	addressField,
	componentForm = {
		street_number: "long_name",
		route: "long_name",
		locality: "long_name",
		postal_code: "long_name",
		country: "long_name"
	};

$('input[name="address"]').focus(function(event) {
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
			var geolocation = {
				lat: position.coords.latitude,
				lng: position.coords.longitude
			};
			var circle = new google.maps.Circle({
				center: geolocation,
				radius: position.coords.accuracy
			});

			addressField.setBounds(circle.getBounds());
		});
	}
});

function initAutocomplete() {
	addressField = new google.maps.places.Autocomplete(document.getElementById("addressField"), {types: ['geocode']});

	addressField.addListener('place_changed', fillInAddress);
}

function fillInAddress() {
	var place = addressField.getPlace(),
		component, i,
		addressType;

	$.each(componentForm, function(name, attr) {
		$('input[name="'+name+'"]').val('');
	});

	$('input[name="place_id"]').val(place.place_id);
	$('input[name="formatted"]').val(place.formatted_address);
	$('input[name="latitude"]').val(place.geometry.location.lat());
	$('input[name="longitude"]').val(place.geometry.location.lng());

	for (i = 0; i < place.address_components.length; i++) {
		addressType = place.address_components[i].types[0];

		if (componentForm[addressType]) {
			$('input[name="'+addressType+'"]').val(place.address_components[i][componentForm[addressType]]);
		}
	}
}
</script>
	<!-- AmCharts setup and what about HighCharts https://www.highcharts.com/stock/demo/spline -->
<script type="text/javascript">

// Réglages du graphique
var mainChart = AmCharts.makeChart("mainChart", {
	type: "serial",
	theme: "light",
	language: "fr",
	mouseWheelZoomEnabled: true,
	zoomOutText: "",
	// legend: {
	// 	useGraphSettings: true,
	// 	position: "right"
	// },
	synchronizeGrid: false,
	autoGridCount: false,
	valueAxes: [{
		id: "vTemperature",
		axisColor: "{{ $colors["temperature"] }}",
		axisThickness: 2,
		axisAlpha: 1,
		position: "left",
		minimum: -10,
		maximum : 50
	}, {
		id: "vWeight",
		axisColor: "{{ $colors["weight"] }}",
		axisThickness: 2,
		axisAlpha: 1,
		position: "right",
		minimum: -10,
		maximum : 120
	}, {
		id: "vHygrometry",
		axisColor: "{{ $colors["hygrometry"] }}",
		axisThickness: 2,
		gridAlpha: 0,
		offset: 50,
		axisAlpha: 1,
		position: "left",
		minimum: 0,
		maximum : 100
	}, {
		id: "vBatteryLevel",
		axisColor: "{{ $colors["batteryLevel"] }}",
		axisThickness: 2,
		gridAlpha: 0,
		offset: 50,
		axisAlpha: 1,
		position: "right",
		minimum: 3.6,
		maximum : 4.30
	}],

	graphs: [{
		valueAxis: "vTemperature",
		id: "vTemperature",
		balloonText: "[[category]]<br>{{ trans('texts.temperature') }} : <b><span style='font-size:14px;'>[[value]]°C</span></b>",
		lineColor: "{{ $colors["temperature"] }}",
		bullet: "round",
		bulletSize: 2,
		bulletBorderThickness: 1,
		hideBulletsCount: 30,
		title: "{{ trans('texts.temperature') }}",
		valueField: "temperature",
		fillAlphas: 0,
		// type: "smoothedLine",
	}, {
		valueAxis: "vWeight",
		id: "vWeight",
		balloonText: "[[category]]<br>{{ trans('texts.weight') }} : <b><span style='font-size:14px;'>[[value]]kg</span></b>",
		lineColor: "{{ $colors["weight"] }}",
		bullet: "round",
		bulletSize: 2,
		bulletBorderThickness: 1,
		hideBulletsCount: 30,
		title: "{{ trans('texts.weight') }}",
		valueField: "weight",
		fillAlphas: 0,
		lineThickness: 2.5,
		// type: "smoothedLine",
	}, {
		valueAxis: "vHygrometry",
		id: "vHygrometry",
		balloonText: "[[category]]<br>{{ trans('texts.hygrometry') }} : <b><span style='font-size:14px;'>[[value]]%</span></b>",
		lineColor: "{{ $colors["hygrometry"] }}",
		bullet: "round",
		bulletSize: 2,
		bulletBorderThickness: 1,
		hideBulletsCount: 30,
		title: "{{ trans('texts.hygrometry') }}",
		valueField: "hygrometry",
		fillAlphas: 0,
		// type: "smoothedLine",
	}, {
		//hidden: true,
		valueAxis: "vBatteryLevel",
		id: "vBatteryLevel",
		balloonText: "[[category]]<br>{{ trans('texts.battery') }} : <b><span style='font-size:14px;'>[[value]]V</span></b>",
		lineColor: "{{ $colors["batteryLevel"] }}",
		bullet: "round",
		bulletSize: 2,
		bulletBorderThickness: 1,
		hideBulletsCount: 30,
		title: "{{ trans('texts.battery_charge') }}",
		valueField: "battery_level",
		fillAlphas: 0,
		// type: "smoothedLine",
	}],

	chartCursor: {
		cursorPosition: "mouse",
		categoryBalloonDateFormat: "DD MMMM YYYY à JJhNN",
	},
	dataDateFormat: "YYYY-MM-DD JJ:NN:SS",
	categoryField: "date",
	categoryAxis: {
		minPeriod: "ss",
		parseDates: true,
		minorGridAlpha: 0.1,
		minorGridEnabled: false,
		position: "bottom",
		gridAlpha: 0
	},
	export: {
		enabled: true,
		divId: "fakeExportButton",
	},
	dataProvider: []
});

// Réglage de la gauge
var gaugeChart = AmCharts.makeChart("gaugeChart", {
	type: "gauge",
	marginBottom: 0,
	marginLeft: 0,
	marginRight: 0,
	marginTop: 0,
	arrows: [
		{
			id: "theArrow",
			value: {{ $batteryLevel }}
		}
	],
	axes: [{
			bottomText: "{{ $batteryLevel }}%",
			bottomTextYOffset: -20,
			endValue: 100,
			id: "theArrow",
			valueInterval: 20,
			bands: [{
					color: "#ea3838",
					endValue: 20,
					id: "GaugeBand-1",
					startValue: 0
				}, {
					color: "#ffac29",
					endValue: 60,
					id: "GaugeBand-2",
					startValue: 20
				}, {
					color: "#00CC00",
					endValue: 100,
					id: "GaugeBand-3",
					innerRadius: "95%",
					startValue: 60
			}]
		}],
	allLabels: [],
	balloon: {},
	titles: []
});
</script>

<!-- Dates pour le calendrier -->
<script type="text/javascript">
//var start = moment("{{ Carbon::now()->subWeek()->format($format) }}");
var start = moment("{{ Carbon::now()->subDays(2)->format($format) }}");
	end = moment("{{ Carbon::now()->format($format) }}");

/*
* Choix des dates pour le calendrier
*/
function updateDateRangeData(picker) {
	if (typeof picker === 'undefined') {
		startDate = start;
		endDate = end;

		$('input[name="date_range"]').val("{{ trans('texts.the_last_2_days') }}"); // Texte à afficher
	} else {
		startDate = picker.startDate;
		endDate = picker.endDate;

		if (picker.chosenLabel != "Personnaliser") {
			$('input[name="date_range"]').val(picker.chosenLabel);
		}
	}

	$('#mainChartLoading').show();
	$('#mainChart').hide();

	$.ajax({
		url: '{{ route('scales.getData') }}',
		type: 'GET',
		data: {
			reference: "{{ $scale->reference }}",
			startDate: startDate.format("YYYY-MM-DD HH:mm:ss"),
			endDate: endDate.format("YYYY-MM-DD HH:mm:ss")
		},
	})

	.done(function(data) {
		SBH.a(this, data);

		mainChart.dataProvider = data;
		mainChart.validateData();

		$('#mainChartLoading').hide();
		$('#mainChart').show();
	});
}
/*
* Une seule date sélectionnable (pas une période), format français
*/
$('input[name="installed_at"]').daterangepicker({
	singleDatePicker: true,
	locale: {
		format: "DD/MM/YYYY",
		weekLabel: "S",
		daysOfWeek: [
			"{{ trans('texts.su') }}",
			"{{ trans('texts.mo') }}",
			"{{ trans('texts.tu') }}",
			"{{ trans('texts.we') }}",
			"{{ trans('texts.th') }}",
			"{{ trans('texts.fr') }}",
			"{{ trans('texts.sa') }}"
		],
		monthNames: [
			"{{ trans('texts.january') }}",
			"{{ trans('texts.february') }}",
			"{{ trans('texts.mars') }}",
			"{{ trans('texts.april') }}",
			"{{ trans('texts.may') }}",
			"{{ trans('texts.june') }}",
			"{{ trans('texts.july') }}",
			"{{ trans('texts.august') }}",
			"{{ trans('texts.september') }}",
			"{{ trans('texts.october') }}",
			"{{ trans('texts.november') }}",
			"{{ trans('texts.december') }}"
		],
		firstDay: 1
	}
});
/*
* Sélection des jours
*/
$('input[name="date_range"]').daterangepicker({
	//startDate: start,
	startDate: moment().add(-2, 'day'), // Date par défaut à 2 jours
	endDate: end,
	minDate: moment("{{ $minDate }}"),
	maxDate: moment("{{ $maxDate }}"),
	showDropdowns: true,
	showWeekNumbers: true,
	alwaysShowCalendars: true,
	ranges: {
		"{{ trans('texts.the_last_2_days') }}": [
			moment().add(-2, "days"),
			moment()
		],
		"{{ trans('texts.the_last_7_days') }}": [
			moment().add(-7, "days"),
			moment()
		],
		"{{ trans('texts.the_last_30_days') }}": [
			moment().add(-30, "days"),
			moment()
		],
	},
	locale: {
		format: "DD/MM/YYYY",
		separator: " - ",
		applyLabel: "{{ trans('texts.apply') }}",
		cancelLabel: "{{ trans('texts.cancel') }}",
		fromLabel: "{{ trans('texts.from_the') }}",
		toLabel: "{{ trans('texts.to_the') }}",
		customRangeLabel: "{{ trans('texts.personalize') }}",
		weekLabel: "S",
		daysOfWeek: [
			"{{ trans('texts.su') }}",
			"{{ trans('texts.mo') }}",
			"{{ trans('texts.tu') }}",
			"{{ trans('texts.we') }}",
			"{{ trans('texts.th') }}",
			"{{ trans('texts.fr') }}",
			"{{ trans('texts.sa') }}"
		],
		monthNames: [
			"{{ trans('texts.january') }}",
			"{{ trans('texts.february') }}",
			"{{ trans('texts.mars') }}",
			"{{ trans('texts.april') }}",
			"{{ trans('texts.may') }}",
			"{{ trans('texts.june') }}",
			"{{ trans('texts.july') }}",
			"{{ trans('texts.august') }}",
			"{{ trans('texts.september') }}",
			"{{ trans('texts.october') }}",
			"{{ trans('texts.november') }}",
			"{{ trans('texts.december') }}"
		],
		firstDay: 1
	}
});
// Met à jour le calendrier à la sélection d'une période
$('input[name="date_range"]').on('apply.daterangepicker', function(ev, picker) {
	updateDateRangeData(picker);
});

updateDateRangeData();
</script>

<script type="text/javascript">
// Modèle de ruche
SBH.VAR.weightReferences = {!! WeightReference::all() !!};

// Récupération des id de "Informations Générales"
SBH.VAR.whichType = {formGroup: $('#whichTypeWrap'), formControl: $('#whichType')};
SBH.VAR.whichModel = {formGroup: $('#whichModelWrap'), formControl: $('#whichModel')};
SBH.VAR.areBodyFramesWaxed = {formGroup: $('#areBodyFramesWaxedWrap'), radioGroup: $('input[name="areBodyFramesWaxed"]')};
SBH.VAR.howManySupers = {formGroup: $('#howManySupersWrap'), textInput: $('input[name="howManySupers"]')};
SBH.VAR.areSuperFramesWaxed = {formGroup: $('#areSuperFramesWaxedWrap'), radioGroup: $('input[name="areSuperFramesWaxed"]')};
SBH.VAR.whichCoverType = {formGroup: $('#whichCoverTypeWrap'), radioGroup: $('input[name="whichCoverType"]')};
SBH.VAR.whichWoodenCoverType = {formGroup: $('#whichWoodenCoverTypeWrap'), radioGroup: $('input[name="whichWoodenCoverType"]')};
SBH.VAR.whichMetalCoverType = {formGroup: $('#whichMetalCoverTypeWrap'), radioGroup: $('input[name="whichMetalCoverType"]')};
SBH.VAR.isThereAnInnerCover = {formGroup: $('#isThereAnInnerCoverWrap'), radioGroup: $('input[name="isThereAnInnerCover"]')};
SBH.VAR.isThereABottomBoard = {formGroup: $('#isThereABottomBoardWrap'), radioGroup: $('input[name="isThereABottomBoard"]')};

// Mail
SBH.VAR.isMailInput = {formGroup: $('#choiceMailInput'), radioGroup: $('input[name="mail_input"]')};
// Tare
SBH.VAR.isTareOk = {formGroup: $('#isThereATareApplied'), radioGroup: $('input[name="isThereATare"]')};

<?php
	// dd($scale->hiveWeight->is_tare_on);
?>
// SBH.VAR.hiveWeight = {!! $scale->hiveWeight !!};

@if (is_null($scale->hiveWeight))
	SBH.VAR.hiveWeight = {};
	console.log("1");

	// @if(!is_null($scale->hiveWeight->is_tare_on))
	// 	<?php //dd($scale->hiveWeight->is_tare_on); ?>
	// 	SBH.VAR.hiveWeight.is_tare_on = {!! $scale->hiveWeight->is_tare_on !!};
	// @endif
@elseif(is_null($scale->hiveWeight->weightReference))
	SBH.VAR.hiveWeight = {!! $scale->hiveWeight !!};
	console.log(SBH.VAR.hiveWeight.is_tare_on);
	<?php
		// dd($scale->hiveWeight->is_tare_on);
	?>
	console.log("2");
@else
	SBH.VAR.hiveWeight = {!! $scale->hiveWeight !!};
	// SBH.VAR.hiveWeight.weightReference = {!! $scale->hiveWeight->weightReference !!};
	console.log("3");
@endif

// Récupération des éléments de la table
/*
@if (is_null($scale->hiveWeight))
	SBH.VAR.hiveWeight = {};
	alert("1")
@elseif(is_null($scale->hiveWeight->weightReference))
	//SBH.VAR.hiveWeight = {!! $scale->hiveWeight !!};
	alert("2")
@else
	// Cas par défaut
	SBH.VAR.hiveWeight = {!! $scale->hiveWeight !!};
	alert("3")
@endif
*/

/**
* Affiche et crée les options de whichType
*/
SBH.FUN.showWhichType = function() {
	var types = [];

	SBH.VAR.whichType.formControl.find('option').remove();
	SBH.VAR.whichType.formControl.append('<option value="" selected></option>');
	$.each(SBH.VAR.weightReferences, function(index, reference) {
		let type = reference.type;
		if (~types.indexOf(type) == 0) {
			types.push(type);
			SBH.VAR.whichType.formControl.append('<option value="'+type+'">'+SBH.UTILS.ucFirst(type)+'</option>');
		}
	});
	SBH.VAR.whichType.formControl.selectpicker('refresh');
	SBH.VAR.whichType.formGroup.show();
}

/**
* A la sélection d'un nouveau type, on affiche ou pas whichModel
*/
SBH.VAR.whichType.formControl.on('changed.bs.select', function(event) {
	var type = $(this).val();

	SBH.FUN.hideWhichModel();
	if (type.length) SBH.FUN.showWhichModel();
});

/**
* Affiche et crée les options de whichModel
*/
SBH.FUN.showWhichModel = function() {
	var type = SBH.VAR.whichType.formControl.val(),
		models = [];

	SBH.VAR.whichModel.formControl.find('option').remove();
	SBH.VAR.whichModel.formControl.append('<option value="" selected></option>');
	$.each(SBH.VAR.weightReferences, function(index, reference) {
		if (reference.type == type) {
			let model = reference.model;
			if (~models.indexOf(model) == 0) {
				models.push(model);
				SBH.VAR.whichModel.formControl.append('<option value="'+reference.id+'">'+model+'</option>');
			}
		}
	});
	SBH.VAR.whichModel.formControl.selectpicker('refresh');
	SBH.VAR.whichModel.formGroup.show();
}

/**
* Dissimule et efface les options de whichModel
*/
SBH.FUN.hideWhichModel = function() {
	SBH.VAR.whichModel.formControl.find('option').remove();
	SBH.VAR.whichModel.formControl.selectpicker('refresh');
	SBH.VAR.whichModel.formGroup.hide();
	SBH.FUN.hideAreBodyFramesWaxed();
}

/**
* la sélection d'un nouveau modèle, on affiche ou pas areBodyFramesWaxed
*/
SBH.VAR.whichModel.formControl.on('changed.bs.select', function(event) {
	var type = SBH.VAR.whichType.formControl.val(),
		model = $(this).val();

	SBH.FUN.hideAreBodyFramesWaxed();
	if (model.length) SBH.FUN.showAreBodyFramesWaxed();
});

/**
* affiche areBodyFramesWaxed
*/
SBH.FUN.showAreBodyFramesWaxed = function() {
	SBH.VAR.areBodyFramesWaxed.radioGroup.prop('checked', false);
	SBH.VAR.areBodyFramesWaxed.formGroup.show();
}

/**
* Dissimule areBodyFramesWaxed
*/
SBH.FUN.hideAreBodyFramesWaxed = function() {
	SBH.VAR.areBodyFramesWaxed.radioGroup.prop('checked', false);
	SBH.VAR.areBodyFramesWaxed.formGroup.hide();
	SBH.FUN.hideHowManySupers();
}

/**
* Au changement areBodyFramesWaxed, on affiche ou pas howManySupers
*/
SBH.VAR.areBodyFramesWaxed.radioGroup.change(function(event) {
	var areBodyFramesWaxed = $(this).val(); // valeur oui/non

	SBH.FUN.hideHowManySupers();
	// Si valeur, affichage
	if (typeof areBodyFramesWaxed !== 'undefined') SBH.FUN.showHowManySupers();
});

/**
* Affiche howManySupers
*/
SBH.FUN.showHowManySupers = function() {
	var type = SBH.VAR.whichType.formControl.val(),
		modelId = SBH.VAR.whichModel.formControl.val(),
		reference;

	$.each(SBH.VAR.weightReferences, function(index, ref) {
		if (ref.type == type && ref.id == modelId) {
			reference = ref;
			return;
		}
	});

	if (reference.super) {
		SBH.VAR.howManySupers.textInput.val(null);
		SBH.VAR.howManySupers.formGroup.show();
	}
	else {
		SBH.FUN.showWhichCoverType();
	}
}

/**
* Dissimule howManySupers
*/
SBH.FUN.hideHowManySupers = function() {
	SBH.VAR.howManySupers.textInput.val(null);
	SBH.VAR.howManySupers.formGroup.hide();
	SBH.FUN.hideAreSuperFramesWaxed();
}

/**
* Au changement howManySupers, on affiche ou pas areSuperFramesWaxed et whichCover
*/
SBH.VAR.howManySupers.textInput.on('keyup change', function(event) {
	var value = parseInt($(this).val());

	$(this).val(value);

	SBH.FUN.hideAreSuperFramesWaxed();
	if (value >= 0 && value <= 4) {
		if (value > 0) {
			SBH.FUN.showAreSuperFramesWaxed();
		}
		else {
			SBH.FUN.showWhichCoverType();
		}
	}
	else {
		$(this).val(null);
	}
});

/**
* Affiche areSuperFramesWaxed
*/
SBH.FUN.showAreSuperFramesWaxed = function() {
	SBH.VAR.areSuperFramesWaxed.radioGroup.prop('checked', false);
	SBH.VAR.areSuperFramesWaxed.formGroup.show();
}

/**
* Dissimule areSuperFramesWaxed
*/
SBH.FUN.hideAreSuperFramesWaxed = function() {
	SBH.VAR.areSuperFramesWaxed.radioGroup.prop('checked', false);
	SBH.VAR.areSuperFramesWaxed.formGroup.hide();
	SBH.FUN.hideWhichCoverType();
}

/**
* Au changement areSuperFramesWaxed, on affiche ou pas whichCover
*/
SBH.VAR.areSuperFramesWaxed.radioGroup.change(function(event) {
	var areSuperFramesWaxed = $(this).val();

	SBH.FUN.hideWhichCoverType();
	if (typeof areSuperFramesWaxed !== 'undefined') SBH.FUN.showWhichCoverType();
});

/**
* affiche whichCoverType
*/
SBH.FUN.showWhichCoverType = function() {
	SBH.VAR.whichCoverType.radioGroup.prop('checked', false);
	SBH.VAR.whichCoverType.formGroup.show();
}

/**
* dissimule whichCoverType
*/
SBH.FUN.hideWhichCoverType = function() {
	SBH.VAR.whichCoverType.radioGroup.prop('checked', false);
	SBH.VAR.whichCoverType.formGroup.hide();
	SBH.FUN.hideWhichWoodenCoverType();
	SBH.FUN.hideWhichMetalCoverType();
}

/**
* au changement whichCoverType, on affiche ou pas whichCover
*/
SBH.VAR.whichCoverType.radioGroup.change(function(event) {
	var whichCoverType = $(this).val();

	if (whichCoverType == 'wooden') SBH.FUN.showWhichWoodenCoverType(); else if (whichCoverType == 'metal') SBH.FUN.showWhichMetalCoverType();
});

/**
* affiche whichWoodenCoverType
*/
SBH.FUN.showWhichWoodenCoverType = function() {
	SBH.VAR.whichWoodenCoverType.radioGroup.prop('checked', false);
	SBH.VAR.whichWoodenCoverType.formGroup.show();
	SBH.FUN.hideWhichMetalCoverType();
}

/**
* dissimule whichWoodenCoverType
*/
SBH.FUN.hideWhichWoodenCoverType = function() {
	SBH.VAR.whichWoodenCoverType.radioGroup.prop('checked', false);
	SBH.VAR.whichWoodenCoverType.formGroup.hide();
	SBH.FUN.hideIsThereAnInnerCover();
}

/**
* au changement whichWoodenCoverType, on affiche ou pas isThereAnInnerCover
*/
SBH.VAR.whichWoodenCoverType.radioGroup.change(function(event) {
	var whichWoodenCoverType = $(this).val();

	SBH.FUN.hideIsThereAnInnerCover();
	if (typeof whichWoodenCoverType !== 'undefined') SBH.FUN.showIsThereAnInnerCover();
});

/**
* affiche whichMetalCoverType
*/
SBH.FUN.showWhichMetalCoverType = function() {
	SBH.VAR.whichMetalCoverType.radioGroup.prop('checked', false);
	SBH.VAR.whichMetalCoverType.formGroup.show();
	SBH.FUN.hideWhichWoodenCoverType();
}

/**
* dissimule whichMetalCoverType
*/
SBH.FUN.hideWhichMetalCoverType = function() {
	SBH.VAR.whichMetalCoverType.radioGroup.prop('checked', false);
	SBH.VAR.whichMetalCoverType.formGroup.hide();
	SBH.FUN.hideIsThereAnInnerCover();
}

/**
 * au changement whichMetalCoverType, on affiche ou pas isThereAnInnerCover
 */
SBH.VAR.whichMetalCoverType.radioGroup.change(function(event) {
	var whichMetalCoverType = $(this).val();

	SBH.FUN.hideIsThereAnInnerCover();
	if (typeof whichMetalCoverType !== 'undefined') SBH.FUN.showIsThereAnInnerCover();
});

/**
 * Affiche isThereAnInnerCover
 */
SBH.FUN.showIsThereAnInnerCover = function() {
	SBH.VAR.isThereAnInnerCover.radioGroup.prop('checked', false);
	SBH.VAR.isThereAnInnerCover.formGroup.show();
}

// dissimule isThereAnInnerCover
SBH.FUN.hideIsThereAnInnerCover = function() {
	SBH.VAR.isThereAnInnerCover.radioGroup.prop('checked', false);
	SBH.VAR.isThereAnInnerCover.formGroup.hide();
	SBH.FUN.hideIsThereABottomBoard();
}

// au changement isThereAnInnerCover, on affiche ou pas isThereABottomBoard
SBH.VAR.isThereAnInnerCover.radioGroup.change(function(event) {
	var isThereAnInnerCover = $(this).val();

	SBH.FUN.hideIsThereABottomBoard();
	if (typeof isThereAnInnerCover !== 'undefined') SBH.FUN.showIsThereABottomBoard();
});

// affiche isThereABottomBoard
SBH.FUN.showIsThereABottomBoard = function() {
	SBH.VAR.isThereABottomBoard.radioGroup.prop('checked', false);
	SBH.VAR.isThereABottomBoard.formGroup.show();
}

// dissimule isThereABottomBoard
SBH.FUN.hideIsThereABottomBoard = function() {
	SBH.VAR.isThereABottomBoard.radioGroup.prop('checked', false);
	SBH.VAR.isThereABottomBoard.formGroup.hide();
}

// au changement isThereABottomBoard, on affiche ou pas isThereABottomBoard
SBH.VAR.isThereABottomBoard.radioGroup.change(function(event) {
	var isThereABottomBoard = $(this).val();
});

// Affiche
(function() {
	// affiche whichType
	SBH.FUN.showWhichType();

	// si le type est déjà sélectionné
	if ( !($.isEmptyObject(SBH.VAR.hiveWeight) || $.isEmptyObject(SBH.VAR.hiveWeight.weight_reference))) {

		// on le sélectionne le type
		SBH.VAR.whichType.formControl.selectpicker('val', SBH.VAR.hiveWeight.weight_reference.type);

		// Affiche whichModel
		SBH.FUN.showWhichModel();
		// On sélectionne whichmodel
		SBH.VAR.whichModel.formControl.selectpicker('val', SBH.VAR.hiveWeight.weight_reference.id);

		// affiche areBodyFramesWaxed
		SBH.FUN.showAreBodyFramesWaxed();
		if (SBH.VAR.hiveWeight.body_frames > 0) {
			SBH.VAR.areBodyFramesWaxed.formGroup.find('[value="0"]').prop('checked', true);
			SBH.VAR.areBodyFramesWaxed.formGroup.find('[value="1"]').prop('checked', false);
		} else if (SBH.VAR.hiveWeight.body_waxed_frames > 0) {
			SBH.VAR.areBodyFramesWaxed.formGroup.find('[value="0"]').prop('checked', false);
			SBH.VAR.areBodyFramesWaxed.formGroup.find('[value="1"]').prop('checked', true);
		} else {
			return;
		}

		// S'il y a une hausse
		if (SBH.VAR.hiveWeight.weight_reference.super) {
			// affiche howManySupers
			SBH.FUN.showHowManySupers();
			SBH.VAR.howManySupers.textInput.val(SBH.VAR.hiveWeight.super);

			if (SBH.VAR.hiveWeight.super > 0) {
				// affiche areSuperFramesWaxed
				SBH.FUN.showAreSuperFramesWaxed();
				if (SBH.VAR.hiveWeight.super_frames > 0) {
					SBH.VAR.areSuperFramesWaxed.formGroup.find('[value="0"]').prop('checked', true);
					SBH.VAR.areSuperFramesWaxed.formGroup.find('[value="1"]').prop('checked', false);
				} else if (SBH.VAR.hiveWeight.super_waxed_frames > 0) {
					SBH.VAR.areSuperFramesWaxed.formGroup.find('[value="0"]').prop('checked', false);
					SBH.VAR.areSuperFramesWaxed.formGroup.find('[value="1"]').prop('checked', true);
				} else {
					return;
				}
			} else if (SBH.VAR.hiveWeight.super == null) {
				return;
			}
		}

		// affiche whichCoverType
		SBH.FUN.showWhichCoverType();
		if (SBH.VAR.hiveWeight.wooden_flat_cover > 0 || SBH.VAR.hiveWeight.wooden_garden_cover > 0) {
			SBH.FUN.showWhichWoodenCoverType();
			SBH.VAR.whichCoverType.formGroup.find('[value="wooden"]').prop('checked', true);
			SBH.VAR.whichCoverType.formGroup.find('[value="metal"]').prop('checked', false);
			if (SBH.VAR.hiveWeight.wooden_flat_cover > 0) {
				SBH.VAR.whichWoodenCoverType.formGroup.find('[value="flat"]').prop('checked', true);
				SBH.VAR.whichWoodenCoverType.formGroup.find('[value="garden"]').prop('checked', false);
			}
			else {
				SBH.VAR.whichWoodenCoverType.formGroup.find('[value="flat"]').prop('checked', false);
				SBH.VAR.whichWoodenCoverType.formGroup.find('[value="garden"]').prop('checked', true);
			}
		} else if (SBH.VAR.hiveWeight.metal_flat_80_cover > 0 || SBH.VAR.hiveWeight.metal_flat_105_cover > 0) {
			SBH.FUN.showWhichMetalCoverType();
			SBH.VAR.whichCoverType.formGroup.find('[value="wooden"]').prop('checked', false);
			SBH.VAR.whichCoverType.formGroup.find('[value="metal"]').prop('checked', true);
			if (SBH.VAR.hiveWeight.metal_flat_80_cover > 0) {
				SBH.VAR.whichMetalCoverType.formGroup.find('[value="80"]').prop('checked', true);
				SBH.VAR.whichMetalCoverType.formGroup.find('[value="105"]').prop('checked', false);
			}
			else {
				SBH.VAR.whichMetalCoverType.formGroup.find('[value="80"]').prop('checked', false);
				SBH.VAR.whichMetalCoverType.formGroup.find('[value="105"]').prop('checked', true);
			}
		} else {
			return;
		}

		// affiche isThereAnInnerCover
		SBH.FUN.showIsThereAnInnerCover();
		if (SBH.VAR.hiveWeight.inner_cover > 0) {
			SBH.VAR.isThereAnInnerCover.formGroup.find('[value="1"]').prop('checked', true);
			SBH.VAR.isThereAnInnerCover.formGroup.find('[value="0"]').prop('checked', false);
		} else if (SBH.VAR.hiveWeight.inner_cover == 0) {
			SBH.VAR.isThereAnInnerCover.formGroup.find('[value="1"]').prop('checked', false);
			SBH.VAR.isThereAnInnerCover.formGroup.find('[value="0"]').prop('checked', true);
		} else {
			return;
		}

		// affiche isThereABottomBoard
		SBH.FUN.showIsThereABottomBoard();
		if (SBH.VAR.hiveWeight.bottom_board > 0) {
			SBH.VAR.isThereABottomBoard.formGroup.find('[value="1"]').prop('checked', true);
			SBH.VAR.isThereABottomBoard.formGroup.find('[value="0"]').prop('checked', false);
		} else if (SBH.VAR.hiveWeight.bottom_board == 0) {
			SBH.VAR.isThereABottomBoard.formGroup.find('[value="1"]').prop('checked', false);
			SBH.VAR.isThereABottomBoard.formGroup.find('[value="0"]').prop('checked', true);
		} else {
			return;
		}

		/**
		* Mail - TODO
		*/
		// Mail - Coche la case en fonction du choix de l'utilisateur
		// Vérification depuis la BDD
		// Trouver la case a cocher puis cocher la correspondante
		/*
		if (SBH.VAR.hiveWeight.mail_input > 0) {
			SBH.VAR.isMailInput.formGroup.find('[value="1"]').prop('checked', true);
			SBH.VAR.isMailInput.formGroup.find('[value="0"]').prop('checked', false);
		} else if (SBH.VAR.hiveWeight.mail_input == 0) {
			SBH.VAR.isMailInput.formGroup.find('[value="1"]').prop('checked', false);
			SBH.VAR.isMailInput.formGroup.find('[value="0"]').prop('checked', true);
		} else {
			return;
		}
		*/

		// Tare
		//SBH.VAR.isTareOk.radioGroup.prop('checked', true);


	// Si l'utilisateur n'a pas remplit les informations de la balance
	}
		/*
		// Mail - Coche la case en fonction du choix de l'utilisateur
		if (SBH.var.mail > 0) {
			SBH.VAR.isMailInput.formGroup.find('[value="1"]').prop('checked', true);
			SBH.VAR.isMailInput.formGroup.find('[value="0"]').prop('checked', false);
		} else if (SBH.var.mail == 0) {
			SBH.VAR.isMailInput.formGroup.find('[value="1"]').prop('checked', false);
			SBH.VAR.isMailInput.radioGroup[0].disabled = true;
			SBH.VAR.isMailInput.formGroup.find('[value="0"]').prop('checked', true);
		} else {
			return;
		}
		*/

		//mail_input de la balance en cours
		/*
		@if(is_null($scale->hiveWeight->mail_input))
			//$scale->hiveWeight->mail_input = 0;
			//SBH.var.mail = $scale->hiveWeight->mail_input;
		@else
			//SBH.var.mail = {!! $scale->hiveWeight->mail_input !!};
		@endif
		*/

		if (SBH.VAR.hiveWeight.is_tare_on == 1) { // oui
			SBH.VAR.isTareOk.formGroup.find('[value="1"]').prop('checked', true);
			SBH.VAR.isTareOk.formGroup.find('[value="0"]').prop('checked', false);
		} else if (SBH.VAR.hiveWeight.is_tare_on == 0) { //non
			SBH.VAR.isTareOk.formGroup.find('[value="1"]').prop('checked', false);
			SBH.VAR.isTareOk.formGroup.find('[value="0"]').prop('checked', true);
		} else {
			return;
		}
})();
</script>

<!-- Autres -->
<script type="text/javascript">
	$('.nav-tabs a[data-toggle="tab"]').on("click", function(event) {
		if ($(this).hasClass("disabled")) {
			event.preventDefault();
			return false;
		}
	});
	// Exportation des données
	$('#exportButton').click(function(event) {
		var startDate = $('input[name="date_range"]').data('daterangepicker').startDate.format("DD/MM/YYYY"),
			endDate = $('input[name="date_range"]').data('daterangepicker').endDate.format("DD/MM/YYYY"),
			subtext;

		$.each($('#formatsList').find('option'), function(optionIndex, option) {
			if ($(option).attr('value') != 'ALL_IN_CSV') {
				subtext = "{{ trans('texts.of_the') }} " + startDate + " {{ trans('texts.to_the') }} " + endDate;
				$(option).data('subtext', subtext);
				$(option).closest('.bootstrap-select').find('.dropdown-menu').find('li[data-original-index="'+optionIndex+'"]').find('.text-muted').text(subtext);
			}
		});

		$('#formatsList').selectpicker('refresh');
		$('#exportMenu').modal('show');
	});
	$('#exportNow').click(function(event) {
		var format = $('#formatsList').val(),
			startDate = $('input[name="date_range"]').data('daterangepicker').startDate.format("DD/MM/YYYY"),
			endDate = $('input[name="date_range"]').data('daterangepicker').endDate.format("DD/MM/YYYY"),
			oldText = $('#exportNow').text(),
			$this = $(this);

		if ($this.attr('data-can-export') == 'true') {
			$this.text("{{ trans('texts.creation_in_progress') }}");
			$this.attr('data-can-export', 'false');

			switch (format) {
				case 'PNG':
					mainChart.export.capture({}, function(data) {
						this.toPNG({}, function(data) {
							this.download(data, this.defaults.formats.PNG.mimeType,
								"{{ trans('texts.scales_data') }} {{ $scale->reference }} {{ trans('texts.between_the') }} "+startDate+" {{ trans('texts.and_the') }} "+endDate+".png");
							$this.text(oldText);
							$this.attr('data-can-export', 'true');
						});
					});
					break;
				case 'JPG':
					mainChart.export.capture({}, function(data) {
						this.toJPG({}, function(data) {
							this.download(data, this.defaults.formats.JPG.mimeType,
							 "{{ trans('texts.scales_data') }} {{ $scale->reference }} {{ trans('texts.between_the') }} "+startDate+" {{ trans('texts.and_the') }} "+endDate+".jpg");
							$this.text(oldText);
							$this.attr('data-can-export', 'true');
						});
					});
					break;
				case 'PDF':
					mainChart.export.capture({}, function(data) {
						this.toPDF({}, function(data) {
							this.download(data, this.defaults.formats.PDF.mimeType,
								"{{ trans('texts.scales_data') }}{{ $scale->reference }} {{ trans('texts.between_the') }} "+startDate+" {{ trans('texts.and_the') }} "+endDate+".pdf");
							$this.text(oldText);
							$this.attr('data-can-export', 'true');
						});
					});
					break;
				case 'CSV':
					mainChart.export.toCSV({}, function(data) {
						this.download(data, this.defaults.formats.CSV.mimeType,
							"{{ trans('texts.scales_data') }} {{ $scale->reference }} {{ trans('texts.between_the') }} "+startDate+" {{ trans('texts.and_the') }} "+endDate+".csv");
						$this.text(oldText);
						$this.attr('data-can-export', 'true');
					});
					break;
				case 'ALL_IN_CSV':
					$.ajax({
						url: '{{ route('scales.getData') }}',
						type: 'GET',
						data: {
							reference: "{{ $scale->reference }}",
							startDate: "{{ $minDate }}",
							endDate: "{{ $maxDate }}"
						},
					})
					.done(function(data) {
						SBH.a(this, data);

						mainChart.export.toCSV({data: data}, function(data) {
							this.download(data, this.defaults.formats.CSV.mimeType,
								"{{ trans('texts.all_scales_data') }} {{ $scale->reference }} {{ trans('texts.since_commissioning') }}.csv");
							$this.text(oldText);
							$this.attr('data-can-export', 'true');
						});
					});
					break;
				default:
					alert("{{ trans('texts.no_format_selected') }}");
					break;
			}
		}
		else {
			alert("{{ trans('texts.another_report') }}");
		}
	});

	// $('#mainChartLegend').selectpicker({
	// 	noneSelectedText: "Rien n'est sélectionné",
	// });
	// $('#mainChartLegend option[value="vBatteryLevel"]').prop('selected', false);
	// $('#mainChartLegend').on('changed.bs.select', function(event) {
	$('.showLegend').change(function(event) {
		var i = 0,
			value = $(this).attr('name'),
			shouldBeVisible = $(this).is(':checked'),
			graph;

		for (i = 0; i < mainChart.graphs.length ; i++) {
			graph = mainChart.graphs[i];

			if (value == graph.id) {
				if (shouldBeVisible) {
					mainChart.graphs[i].hidden = false;
				} else {
					mainChart.graphs[i].hidden = true;
				}
				break;
			}
		}
		mainChart.validateData();
	});

@if ($scale->isGeolocated())
	/*
	* chargement des données météorologiques
	*/
	$.ajax({
		url: 'http://api.apixu.com/v1/current.json?key=357cc18e7ec541188f8191614172704&q={{ $scale->place->latitude }},{{ $scale->place->longitude }}',
		type: 'GET',
	})
	.done(function(data) {
		SBH.a(this, data);

		$('#wIcon').attr('src', data.current.condition.icon);
		$('#wTempC').text(data.current.temp_c);
		$('#wFeelsLikeC').text(data.current.feelslike_c);
		$('#wWindKPH').text(data.current.wind_kph);
		// $('#wWindDir').text(data.current.wind_dir);
		$('#wPressureMB').text(data.current.pressure_mb);
		$('#wPrecipMM').text(data.current.precip_mm);
		$('#wHumidity').text(data.current.humidity);
		$('#wCloud').text(data.current.cloud);
		$('#weatherTable').css('display', 'table');
		$('#weatherLegend').text("{{ trans('texts.weather_in') }}" + data.location.name);
	})
	.fail(function() {
		$('#weatherLegend').text("{{ trans('texts.weather_unavailable') }}");
		$('#weatherPlugin').html('<i>{{ trans('texts.weather_unavailable') }}...</i>');
	});
@endif
</script>
@endsection
