@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
				@if (count($hives)>0)
					<div class="panel-heading">{{ trans('texts.get_one_of_hive') }}</div>
					<div class="panel-body">
						{!! Form::open(['url' => route('hives.access')]) !!}
						<div class="form-group">
							<select class="form-control selectpicker" data-live-search="true" name="hive_id">
								@foreach ($hives as $hive)
									<option value="{{ $hive->id }}" data-subtext="{{ $hive->alias }}">{{ $hive->reference }}</option>
								@endforeach
							</select>
						</div>
						{!! Form::submit('Accéder', ['class' => 'btn btn-info pull-left']) !!}
						{!! Form::close() !!}
					</div>
				@else
					<div class="panel-heading">{{ trans('texts.add_first_hive') }}</div> 
					<div class="panel-body">
						{{ trans('texts.hive_not_yet_saved') }}
						<br>
						<a href="{{ route('hives.add') }}">{{ trans('texts.add_hive_by_reference') }}</a> 
					</div>
				@endif
				</div>
			</div>
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading"> {{ trans('texts.on_the_map') }} </div> 
					<div class="panel-body" id="google-map"></div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('style')
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/css/bootstrap-select.min.css">
@endsection

@section('script')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>
	<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAzWE3TFj2s2IRUk5tlw-uUMzvC7NVy_wo&callback=initMap"></script>
	<script type="text/javascript">
		var map,
			googleMap = document.getElementById('google-map'),
			marker, markers = [];

		function initMap() {
			var coords = {lat: 46.8, lng: 2.0},
				infoWindow = new google.maps.InfoWindow(),
				map = new google.maps.Map(googleMap, {
					zoom: 6,
					center: coords,
					mapTypeId: 'terrain',
					disableDefaultUI: true
				});

			@foreach($hives as $hive)
			@if ($hive->isGeolocated())
			marker = new google.maps.Marker({
				position: {lat: {{ $hive->latitude }}, lng: {{ $hive->longitude }}},
				map: map,
				animation: google.maps.Animation.DROP,
				icon: "{{ asset('icons/hive.png') }}",
				title: "{{ $hive->reference }}",
			});
			marker.addListener('click', function() {
				map.setZoom(8);
				map.panTo(this.getPosition());
				// map.setCenter(this.getPosition());
				infoWindow.setContent(''
					+ '<h1>Ruche {{ $hive->reference }}</h1>'
					+ '<p>Vous pouvez retrouver une analyse plus avancée de cette ruche <a href="{{ route('hives.see', ['id' => $hive->id]) }}">ici</a></p>'
				);
				// infoWindow.setPath(marker.getPosition());
				infoWindow.open(map, this);
			});
			markers.push(marker);
			@endif
			@endforeach
		}

		(function () {
			googleMap.style.setProperty('height', '600px');
		})();
	</script>
@endsection
