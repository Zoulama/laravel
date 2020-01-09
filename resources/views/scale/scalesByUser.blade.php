@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        @if(isset($user[0]))
            <h2 class="text-center">{{ trans('texts.scale_of') }} {{ $user[0]->last_name .' '. $user[0]->first_name}}</h2>
        @endif 
        <table class="col-xl-12 table table-bordered table-striped table-condensed">
            <thead>
                <th>{{ trans('texts.id') }}</th>
                <th>{{ trans('texts.name_tab') }}</th>
                <th>{{ trans('texts.reference') }}</th>
                <th>{{ trans('texts.imei') }}</th>
                <th>{{ trans('texts.commentary') }}</th>
                <th>{{ trans('texts.weight') }}</th>
                <th>{{ trans('texts.tare') }}</th>
                <th>{{ trans('texts.weight_coefficient') }}</th> 
            </thead>
            <tbody>
                <!-- Si administrateur -->
                @if($user[0]->id === 1)
                    @foreach($scalesAdmin as $scale)
                        <tr>
                            <td>{{ $scale->id }}</td>
                            <td>{{ $scale->alias }}</td>
                            <td><a href="{{ route('scales.see', ["reference" => $scale->reference]) }}">{{ $scale->reference }}</a></td>
                            <td>{{ $scale->imei }}</td>
                            <td>{{ $scale->comment }}</td>
                            <td>{{ $scale->hive_weight }}</td>
                            <td>{{ $scale->tare }}</td>
                            <td>{{ $scale->weight_coefficient }}</td>
                        </tr>
                    @endforeach
                <!-- Si propriétaire standard -->
                @else
                    @for($i = 0; $i < count($scale_user); $i++)
                        @foreach($scales as $scale)
                            @if($scale_user[$i]->scale_id === $scale->id )
                            <tr>
                                <td>{{ $scale->id }}</td>
                                <td>{{ $scale->alias }}</td>
                                <td><a href="{{ route('scales.see', ["reference" => $scale->reference]) }}">{{ $scale->reference }}</a></td>
                                <td>{{ $scale->imei }}</td>
                                <td>{{ $scale->comment }}</td>
                                <td>{{ $scale->hive_weight }}</td>
                                <td>{{ $scale->tare }}</td>
                                <td>{{ $scale->weight_coefficient }}</td>
                            </tr>
                            @endif
                        @endforeach
                    @endfor
                @endif
           </tbody>
        </table>
    </div>
</div>
@endsection

@section('style')
<style type="text/css">
</style>
@endsection

@section('script')
<script>
</script>
@endsection


               