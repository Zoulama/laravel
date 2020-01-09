<?php
use \Carbon\Carbon;

//GOOGLE API KEY: key=AIzaSyAzWE3TFj2s2IRUk5tlw-uUMzvC7NVy_wo

// pour les dates en français, en attendant une solution plus élégante
Carbon::setLocale('fr');
date_default_timezone_set('Europe/Paris');
$version = time();
?>

@extends('layouts.app')

@section('content')
<h1 style="text-align: center">{{ trans('texts.adding') }}</h1> 
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{ trans('texts.create_new_scale_you') }}</div>
                <div class="panel-body">
                {!! Form::open(['url' => route('scales.store')]) !!}
                    <div class="form-group {!! $errors->has('imei') ? 'has-error' : '' !!}">
                        {!! Form::label('imei', trans('texts.the_imei_of_scale')) !!}
                        {!! Form::text('imei', null, ['class' => 'form-control', 'placeholder' => trans('texts.imei_of_scale')]) !!}
                        {!! $errors->first('imei', '<small class="help-block">:message</small>') !!}
                    </div>
                    <div class="form-group {!! $errors->has('alias') ? 'has-error' : '' !!}">
                        {!! Form::label('alias', trans('texts.name')) !!}
                        {!! Form::text('alias', null, ['class' => 'form-control']) !!}
                        {!! $errors->first('alias', '<small class="help-block">:message</small>') !!}
                    </div>
                    <div class="form-group {!! $errors->has('address') ? 'has-error' : '' !!}">
                        {!! Form::label('address', trans('texts.address')) !!}
                        {!! Form::text('address', null, ['class' => 'form-control', 'id' => 'addressField']) !!}
                        {!! $errors->first('address', '<small class="help-block">:message</small>') !!}
                    </div>
                    <div class="form-group {!! $errors->has('installed_at') ? 'has-error' : '' !!}">
                        {!! Form::label('installed_at', trans('texts.installed_on')) !!}
                        {!! Form::text('installed_at', Carbon::now()->format('d/m/Y'), ['class' => 'form-control']) !!}
                        {!! $errors->first('installed_at', '<small class="help-block">:message</small>') !!}
                    </div>
                    <div class="form-group {!! $errors->has('comment') ? 'has-error' : '' !!}">
                        {!! Form::label('comment', trans('texts.details_to')) !!}
                        {!! Form::textarea('comment', null, ['class' => 'form-control', 'rows' => '5']) !!}
                        {!! $errors->first('comment', '<small class="help-block">:message</small>') !!}
                    </div>
                    <div class="form-group">
                        {!! Form::submit(trans('texts.create'), ['class' => 'btn btn-info pull-right']) !!}
                    </div>
                    {!! Form::hidden('formatted', null) !!}
                    {!! Form::hidden('place_id', null) !!}
                    {!! Form::hidden('street_number', null) !!}
                    {!! Form::hidden('route', null) !!}
                    {!! Form::hidden('locality', null) !!}
                    {!! Form::hidden('postal_code', null) !!}
                    {!! Form::hidden('country', null) !!}
                    {!! Form::hidden('latitude', null) !!}
                    {!! Form::hidden('longitude', null) !!}
                    {!! Form::hidden('altitude', null) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection


@section('script')
    <script src="{{ asset('plugins/bootstrap-daterangepicker/2.1.25/js/daterangepicker.js') }}?v={{ $version }}"></script>

    <!--
    Google setup
    -->
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

    <!--
    DateRangePicker setup
    -->
    <script type="text/javascript">
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
            singleDatePicker: true,
            locale: {
                format: "DD/MM/YYYY",
                weekLabel: "S",
                daysOfWeek: [
                    "Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"
                ],
                monthNames: [
                    "Janvier", "Février", "Mars", "Avril", "Mai", "Juin",
                    "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"
                ],
                firstDay: 1
            }
        });
    })();
    </script>
@endsection