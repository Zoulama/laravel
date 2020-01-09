@extends('emails.master_user')

@section('body')
    <h2></h2>
    <p>
        {{ trans('texts.hello') }} {{$userInfo['fullName']}}
    </p>

    <p>
        {{ trans('texts.balance_info') }}
    </p>

    <p>

    </p>
    &nbsp;
    <div>
        <center>
            @foreach($dataAlerts as $dataAlert)
                <p>
                    <b>Nom de la balance : </b> {{$dataAlert['alias']}} <br>
                    @if($dataAlert['lowBattery'])
                        <b> Betterie faible : </b> {{$dataAlert['batteryState']}} % <br>
                    @endif

                    @if(!$dataAlert['delocalise'])
                        <b>  {{$dataAlert['located']}} </b> <br> <br>
                    @endif

                </p>
            @endforeach
        </center>
    </div>
    <div>
        <p>{{ trans('texts.app_name') }}</p>
    </div>
@stop
