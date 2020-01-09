<?php
use \Carbon\Carbon;
// dd($errors);
?>

@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-sm-4">
				@include('hive.templates.time-settings-panel')
				@include('hive.templates.stock-panel')
				@include('hive.templates.weather-panel')
				@include('hive.templates.battery-level-panel')
				@include('hive.templates.details-panel')
			</div>
			<div class="col-sm-8">
				@include('hive.templates.analysis-panel')
				@include('hive.templates.weight-panel')
				@include('hive.templates.temperature-panel')
				@include('hive.templates.hygrometry-panel')
				@include('hive.templates.noise-panel')
			</div>
		</div>
	</div>
@endsection

@section('style')
	<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap/3/css/bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
@endsection

@section('script')
	<script type="text/javascript" src="//cdn.jsdelivr.net/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
	<script type="text/javascript" src="https://www.amcharts.com/lib/3/amcharts.js"></script>
	<script type="text/javascript" src="https://www.amcharts.com/lib/3/serial.js"></script>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script type="text/javascript">
		// création du trigger resizeEnd    
		$(window).resize(function() {
			if (this.resizeTO)
				clearTimeout(this.resizeTO);
			
			this.resizeTO = setTimeout(function() {
				$(this).trigger('resizeEnd');
			}, 500);
		});

		// redraw si resize
		$(window).on('resizeEnd', function() {
			redrawCharts();
		});
		/*
		 *
		 * quelques fonctions
		 *
		 */
		/*
		 * rend le graphique du stock
		 */
		function drawStockChart() {
			// var data = google.visualization.arrayToDataTable([
			// 	['Language', 'Stock (en poucentage)'],
			// 	['Miel',  4],
			// 	['Cire',  3],
			// 	['Abeille', 2],
			// 	['Propolis', 1]
			// ]);

			// var options = {
			// 	legend: 'none',
			// 	pieSliceText: 'label',
			// 	pieHole: 0.5,
			// 	pieSliceTextStyle: {
			// 		color: 'black',
			// 	},
			// 	slices: {
			// 		0: { color: 'rgb(233, 203, 38)' }, // 233 203 38
			// 		1: { color: 'rgb(82, 176, 59)' }, // 82 176 59
			// 		2: { color: 'rgb(236, 141, 24)' }, // 236 141 24
			// 		3: { color: 'rgb(64, 153, 237)' }, // 64 153 237
			// 	},
			// 	chartArea: { width: "100%", height: "100%" }
			// };

			// var chart = new google.visualization.PieChart(document.getElementById('stockChart'));
			// chart.draw(data, options);
		}
		/*
		 * rend le graphique des poids
		 */
		$('#printWeightChart').click(function(event) {
			console.log(weightChart);
			if (weightChart!==undefined) {
				window.open(weightChart.getImageURI(), "_blank");
			}
		});
		var weightChart;
		function drawWeightChart() {
			$.ajax({
				url: '{{ route('hives.json.weights') }}',
				type: 'GET',
				data: {
					hive_id: {{ $hive->id }},
					from_date: $('input[name="from_date"]').val(),
					to_date: $('input[name="to_date"]').val(),
				},
			})
			.done(function(weights) {
				console.log("Les poids", weights);
				$('#weightChart').height(300);

				if (weights.length==0) {
					$('#weightChart').find('i').text("{{ trans('texts.no_weight_data_could_be_recovered') }}");
				}
				else {
					var data = google.visualization.arrayToDataTable(weights);

					var options = {
						titlePosition: 'none',
						curveType: 'none',
						legend: { position: 'bottom' },
						chartArea: { width: "80%", height: "auto", top: '10' }
					};

					weightChart = new google.visualization.LineChart(document.getElementById('weightChart'));

					weightChart.draw(data, options);
				}
			});
		}
		/*
		 * rend le graphique des températures
		 */
		$('#printTemperatureChart').click(function(event) {
			console.log(temperatureChart);
			if (temperatureChart!==undefined) {
				window.open(temperatureChart.getImageURI(), "_blank");
			}
		});
		var temperatureChart;
		function drawTemperatureChart() {
			$.ajax({
				url: '{{ route('hives.json.temperatures') }}',
				type: 'GET',
				data: {
					hive_id: {{ $hive->id }},
					from_date: $('input[name="from_date"]').val(),
					to_date: $('input[name="to_date"]').val(),
				},
			})
			.done(function(temperatures) {
				console.log("Les températures", temperatures);
				$('#temperatureChart').height(300);

				if (temperatures.length==0) {
					$('#temperatureChart').find('i').text("{{ trans('texts.no_temperature_data_couldbe_retrieved') }}");
				}
				else {
					var data = google.visualization.arrayToDataTable(temperatures);

					var options = {
						titlePosition: 'none',
						curveType: 'none',
						legend: { position: 'bottom' },
						chartArea: { width: "80%", height: "auto", top: '10' }
					};

					temperatureChart = new google.visualization.LineChart(document.getElementById('temperatureChart'));

					temperatureChart.draw(data, options);
				}
			});
		}
		/*
		 * rend le graphique des sons
		 */
		$('#printNoiseChart').click(function(event) {
			console.log(noiseChart);
			if (noiseChart!==undefined) {
				window.open(noiseChart.getImageURI(), "_blank");
			}
		});
		var noiseChart;
		function drawNoiseChart() {
			$.ajax({
				url: '{{ route('hives.json.noises') }}',
				type: 'GET',
				data: {
					hive_id: {{ $hive->id }},
					from_date: $('input[name="from_date"]').val(),
					to_date: $('input[name="to_date"]').val(),
				},
			})
			.done(function(noises) {
				console.log("Les sons", noises);
				$('#noiseChart').height(300);

				if (noises.length==0) {
					$('#noiseChart').find('i').text("{{ trans('texts.no_sound_data_could_be_retrieved') }}");
				}
				else {
					var data = google.visualization.arrayToDataTable(noises);

					var options = {
						titlePosition: 'none',
						curveType: 'none',
						legend: { position: 'bottom' },
						chartArea: { width: "80%", height: "auto", top: '10' }
					};

					noiseChart = new google.visualization.LineChart(document.getElementById('noiseChart'));

					noiseChart.draw(data, options);
				}
			});
		}
		/*
		 * rend le graphique de l'hygrométrie
		 */
		$('#printHygrometryChart').click(function(event) {
			console.log(hygrometryChart);
			if (hygrometryChart!==undefined) {
				window.open(hygrometryChart.getImageURI(), "_blank");
			}
		});
		var hygrometryChart;
		function drawHygrometryChart() {
			$.ajax({
				url: '{{ route('hives.json.hygrometries') }}',
				type: 'GET',
				data: {
					hive_id: {{ $hive->id }},
					from_date: $('input[name="from_date"]').val(),
					to_date: $('input[name="to_date"]').val(),
				},
			})
			.done(function(hygrometries) {
				console.log("L'hygrométrie", hygrometries);
				$('#hygrometryChart').height(300);

				if (hygrometries.length==0) {
					$('#hygrometryChart').find('i').text("{{ trans('texts.no_hygrometry_data_could_be_recovered') }}");
				}
				else {
					var data = google.visualization.arrayToDataTable(hygrometries);

					var options = {
						titlePosition: 'none',
						curveType: 'none',
						legend: { position: 'bottom' },
						chartArea: { width: "80%", height: "auto", top: '10' }
					};

					hygrometryChart = new google.visualization.LineChart(document.getElementById('hygrometryChart'));

					hygrometryChart.draw(data, options);
				}
			});
		}
		/*
		 * rend la jauge de la batterie
		 */
		function drawBatteryLevelGauge() {
			<?php
			list($bLoading, $bLevel) = $hive->getCurrentBatteryState();
			?>
			@if ( ! is_null($bLevel))
			var data = google.visualization.arrayToDataTable([
				['Label', 'Value'],
				['Charge', {{ $bLevel }}]
			]);

			var options = {
				redFrom: 90,
				redTo: 100,
				yellowFrom:75,
				yellowTo: 90,
				minorTicks: 5,
			};

			var chart = new google.visualization.Gauge(document.getElementById('batteryLevelGauge'));

			chart.draw(data, options);
			@endif
		}
		/*
		 * redessine tous les graphiques
		 */
		function redrawCharts() {
			google.charts.setOnLoadCallback(drawNoiseChart);
			google.charts.setOnLoadCallback(drawTemperatureChart);
			google.charts.setOnLoadCallback(drawWeightChart);
			google.charts.setOnLoadCallback(drawStockChart);
			google.charts.setOnLoadCallback(drawHygrometryChart);
			google.charts.setOnLoadCallback(drawBatteryLevelGauge);
		}

		(function () {
			/*
			 * chargement du plugin daterangepicker
			 */
			var fromDate = $('input[name="from_date"]').val();
				fromDate = moment(fromDate);
			var toDate = $('input[name="to_date"]').val();
				toDate = moment(toDate);
			/*
			 * une seule date sélectionnable (pas une période), format français
			 */
			$('input[name="installed_at"]').daterangepicker({
				"singleDatePicker": true,
				"locale": {
					"format": "DD/MM/YYYY",
					"weekLabel": "S",
					"daysOfWeek": [
						"{{ trans('texts.su') }}", 
						"{{ trans('texts.mo') }}", 
						"{{ trans('texts.tu') }}", 
						"{{ trans('texts.we') }}",
						"{{ trans('texts.th') }}",
						"{{ trans('texts.fr') }}", 
						"{{ trans('texts.sa') }}"
					],
					"monthNames": [
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
					"firstDay": 1
				}
			});
			/*
			 * une période, format français
			 */
			$('input[name="date_range"]').daterangepicker({
				startDate: fromDate,
				endDate: toDate,
				ranges: {
					"{{ trans('texts.the_last_24_hours') }}": [moment().subtract(1, 'days'), moment()],
					"{{ trans('texts.the_last_7_days') }}": [moment().subtract(1, 'weeks'), moment()],
					"{{ trans('texts.the_last_30_days') }}": [moment().subtract(1, 'months'), moment()]
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
			/*
			 * lorsqu'une nouvelle période est définie
			 */
			$('input[name="date_range"]').on('apply.daterangepicker', function(event, picker) {
				var fromDate = picker.startDate.format('YYYY-MM-DD HH:mm:ss');
				var toDate = picker.endDate.format('YYYY-MM-DD HH:mm:ss');

				$('input[name="from_date"]').val(fromDate);
				$('input[name="to_date"]').val(toDate);

				$('span.period').text('{{ trans('texts.from_the') }} '+picker.startDate.format("DD/MM/YYYY")+' {{ trans('texts.to_the') }} '+picker.endDate.format("DD/MM/YYYY"));

				redrawCharts();
			});

			$('span.period').text('{{ trans('texts.from_the') }} '+fromDate.format("DD/MM/YYYY")+' {{ trans('texts.to_the') }} '+toDate.format("DD/MM/YYYY"));

			/*
			 * chargement des graphiques la première fois
			 */
			google.charts.load("current", {packages:["corechart", "gauge"]});
			redrawCharts();

		@if ($hive->isGeolocated())
			/*
			 * chargement des données météorologiques
			 */
			var xhr = new XMLHttpRequest();
			xhr.onreadystatechange = function() {
				if (xhr.readyState === XMLHttpRequest.DONE) {
					if (xhr.status === 200) {
						var data = JSON.parse(xhr.responseText);
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
					} else {
						$('#weatherPlugin .panel-heading').html('{{ trans('texts.weather_unavailable') }}');
						$('#weatherPlugin .panel-body').html('<i>{{ trans('texts.weather_unavailable') }}...</i>');
					}
				}
			};
			xhr.open("GET", 'http://api.apixu.com/v1/current.json?key=357cc18e7ec541188f8191614172704&q={{ $hive->latitude }},{{ $hive->longitude }}', true);
			xhr.send();
		@endif

		})();
	</script>
@endsection