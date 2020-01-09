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
            @foreach($scalesInfos as $scalesInfo)
                <p>
                <p><b> Nom de la balance : </b> {{$scalesInfo['alias']}} <br> </p>
                <p>
                    <b>Temperature : </b> {{$scalesInfo['temperature']}} °C<br>
                    <b>Poids : </b> {{$scalesInfo['weight']}} KG<br>
                    <b>hygrometry : </b> {{$scalesInfo['hygrometry']}} %<br>
                    <b>Charge de la betterie : </b> {{$scalesInfo['currentBatteryState'][1]}} % <br>
                </p>
                </p>
            @endforeach
        </center>
    </div>
    <div>
        <p>{{ trans('texts.app_name') }}</p>
    </div>
@stop
