<?php
    $version = time();
?>
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                @if (count($scales) > 0)
                    <div class="panel-heading">{{ trans('texts.access_of_you_scale') }}</div> 
                    <div class="panel-body">
                        <!-- Route pour l'envoit des données routes/web.php -->
                        {!! Form::open(['url' => route('scales.access')]) !!}
                        <div class="form-group">
                            <select class="form-control selectpicker" data-live-search="true" name="reference">
                                @foreach ($scales as $scale)
                                    <?php
                                        $locality = $scale->getPlace('locality');

                                        if (!is_null($locality)) {
                                            $locality = "à {$locality} ";
                                        }
                                    ?>
                                    <option value="{{ $scale->reference }}" data-subtext="{{ $locality }}({{ $scale->reference }})">{{ $scale->getAlias() }}</option>
                                @endforeach
                            </select>
                        </div>
                        {!! Form::submit(trans('texts.to_access'), ['class' => 'btn btn-info pull-left']) !!}
                        {!! Form::close() !!}
                    </div>
                @else
                    <div class="panel-heading">{{ trans('texts.add_first_scale') }}</div>
                    <div class="panel-body">
                        {{ trans('texts.scale_not_yet_regitered') }} 
                        <br>
                        <a href="{{ route('scales.add') }}">{{ trans('texts.add_scale_by_reference') }}</a>
                    </div>
                @endif
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ trans('texts.ont_the_map') }}</div>
                    <div class="panel-body" id="google-map"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/css/bootstrap-select.min.css"> --}}
@endsection

@section('script')
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script> --}}
    <script src="{{ asset('plugins/googlemaps-markerclustererplus/2.1.2/src/markerclusterer.js') }}?v={{ $version }}"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAzWE3TFj2s2IRUk5tlw-uUMzvC7NVy_wo&callback=initMap"></script>
    <script type="text/javascript">
        var markerCluster,
            googleMap = document.getElementById('google-map'),
            markers = [];

        function initMap() {
            var coords = {lat: 46.8, lng: 2.0},
                infoWindow = new google.maps.InfoWindow(),
                marker,
                // https://developers.google.com/maps/documentation/javascript/controls
                map = new google.maps.Map(googleMap, {
                    zoom: 6,
                    center: coords,
                    mapTypeId: 'satellite',
                    // disableDefaultUI: true,
                    // mapTypeControl: true,
                    // mapTypeControlOptions: {
                    //  style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                    //  position: google.maps.ControlPosition.TOP_CENTER
                    // },
                    // zoomControl: true,
                    // zoomControlOptions: {
                    //  position: google.maps.ControlPosition.LEFT_CENTER
                    // },
                    // scaleControl: true,
                    // streetViewControl: true,
                    // streetViewControlOptions: {
                    //  position: google.maps.ControlPosition.LEFT_TOP
                    // },
                });

            // pour éviter d'avoir les zooms à 45°
            map.setTilt(0);

            @foreach($scales as $scale)
            @if ($scale->isGeolocated())
            marker = new google.maps.Marker({
                position: {lat: {{ $scale->place->latitude }}, lng: {{ $scale->place->longitude }}},
                map: map,
                animation: google.maps.Animation.DROP,
                // icon: "{{ asset('icons/hive.png') }}",
                title: "{{ $scale->getAlias() }}",
            });

            google.maps.event.addListener(marker, 'click', function() {
                map.setZoom(20);
                map.panTo(this.getPosition());
                // map.setCenter(this.getPosition());
                infoWindow.setContent('\
                    <h3 class="display-3">\
                       {{ $scale->getAlias() }}\
                    </h3>\
                    <p>\
                        Quelques éléments :\
                        <ul>\
                            <li>Sa référence : {{ $scale->reference }}</li>\
                            <li>Géolocalisation : {{ $scale->getPlace("formatted") }}</li>\
                            <li>Le nombre de relevés disponibles : {{ $scale->reports->count() }}</li>\
                            <li>Pour plus de détails concernant cette balance : <a href="{{ route('scales.see', ['reference' => $scale->reference]) }}">(cliquez ici)</a></li>\
                        </ul>\
                    </p>\
                ');
                // infoWindow.setPath(marker.getPosition());
                infoWindow.open(map, this);
            });

            markers.push(marker);
            @endif
            @endforeach

            markerCluster = new MarkerClusterer(map, markers, {
                imagePath: '{{ asset('plugins/googlemaps-markerclustererplus/2.1.2/images/m') }}',
                zoomOnClick: true,
                maxZoom: 19
            });
        }

        (function () {
            googleMap.style.setProperty('height', '600px');
        })();
    </script>
@endsection
