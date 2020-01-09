<?php
    use \Carbon\Carbon;
    Carbon::setLocale('fr'); // date en français en attendant mieux
    date_default_timezone_set('Europe/Paris');
    $version = time();
?>
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <table class="col-xl-12 table table-bordered table-striped table-condensed">
            <thead>
                <th>&Eacute;tat</th>
                <th>@sortablelink('id', 'ID')</th>
                <th>{{ trans('texts.owner') }}</th>
                <th>@sortablelink('alias',trans('texts.name_of_scale'))</th> 
                <th>{{ trans('texts.reference') }}</th>
                <!-- <th>@sortablelink('ScaleReport.scale_id', 'Relevés')</th> -->
                <th>{{ trans('texts.statements') }}</th>
                <th>@sortablelink('imei', 'IMEI')</th>
                <th>{{ trans('texts.deleting') }} <br> {{ trans('texts.some_scales') }}</th>
                <th>{{ trans('texts.deleting') }} <br> {{ trans('texts.assignments') }}</th>
                <th>{{ trans('texts.deleting') }} <br> {{ trans('texts.some_statements') }}</th>
            </thead>
            <tbody>
            @foreach ($scales as $scale)
                @php $owners = $scale->owners; @endphp
                <tr>
                    <!-- Si Tare vide -->
                    @if (is_null($scale->tare))
                        <td class="bg-danger">T0 {{ trans('texts.ongoing') }}</td>
                        <td>{{ $scale->id }}</td>
                        <td class="text-center">
                            @if (count($owners) > 0)
                                @foreach ($owners as $owner)
                                    {!! '<strong>'. $owner->last_name . '</strong>' . ' ' . $owner->first_name !!}
                                @endforeach
                            @else
                                {!! '<div><strong>'. '-' .'</strong></div>' !!}
                            @endif
                        </td>
                        <td>{{ $scale->alias  }}</td>
                        <td><a href="{{ route('scales.see', ["reference" => $scale->reference]) }}">{{ $scale->reference }}</a></td>
                        <td>{{ trans('texts.tare_is_missing') }}</td>
                        <td>{{ $scale->imei }}</td>
                        <td>
                        {!! Form::open(['url' => route('scales.deleteScale', $scale->reference)]) !!}
                            {!! Form::submit('X', ['class' => 'delete btn btn-danger center-block',
                                    'onClick' => 'return confirm(\''.trans('texts.delete_balance_confirm').'\')'
                                ] ) 
                            !!}
                        {!! Form::close() !!}
                        </td>
                        <td>
                        {!! Form::open(['url' => route('scales.deleteAffectation', $scale->reference)]) !!}
                            {!! Form::submit('X', ['class' => 'delete btn btn-warning center-block',
                                'onClick' => 'return confirm(\''.trans('texts.delete_link_user_confirm').'\')'
                                ] ) 
                            !!}
                        {!! Form::close() !!}
                        </td>
                        <td>
                        {!! Form::open(['url' => route('scales.deleteReports', $scale->reference)]) !!}
                            {!! Form::submit('X', ['class' => 'delete btn btn-info center-block',
                                'onClick' => 'return confirm(\''.trans('texts.delete_statements_confirm').'\')'
                                ] ) 
                            !!}
                        {!! Form::close() !!}
                        </td>

                    <!-- Si poids vide -->
                    @elseif (is_null($scale->weight_coefficient))
                        <td class="bg-warning">T1 {{ trans('texts.ongoing') }}</td>
                        <td>{{ $scale->id }}</td>
                        <td >
                            @if (count($owners) > 0)
                                @foreach ($owners as $owner)
                                    {!! '<strong>'. $owner->last_name . '</strong>' . ' ' . $owner->first_name !!}
                                @endforeach
                            @else
                                {!! '<div class="text-center"><strong>'. '-' .'</strong></div>' !!}
                            @endif
                        </td>
                        <td>{{ $scale->alias }}</td>
                        <td><a href="{{ route('scales.see', ["reference" => $scale->reference]) }}">{{ $scale->reference }}</a></td>
                        <td>{{ trans('texts.weight_correction_coefficient_missing') }}</td> 
                        <td>{{ $scale->imei }}</td>
                        <td>
                        {!! Form::open(['url' => route('scales.deleteScale', $scale->reference)]) !!}
                            {!! Form::submit('X', ['class' => 'delete btn btn-danger center-block',
                                    'onClick' => 'return confirm(\''.trans('texts.delete_balance_confirm').'\')'
                                ] ) 
                            !!}
                        {!! Form::close() !!}
                        </td>
                        <td>
                        {!! Form::open(['url' => route('scales.deleteAffectation', $scale->reference)]) !!}
                            {!! Form::submit('X', ['class' => 'delete btn btn-warning center-block',
                                'onClick' => 'return confirm(\''.trans('texts.delete_link_user_confirm').'\')'
                                ] ) 
                            !!}
                        {!! Form::close() !!}
                        </td>
                        <td>
                        {!! Form::open(['url' => route('scales.deleteReports', $scale->reference)]) !!}
                            {!! Form::submit('X', ['class' => 'delete btn btn-info center-block',
                                'onClick' => 'return confirm(\''.trans('texts.delete_statements_confirm').'\')'
                                ] ) 
                            !!}
                        {!! Form::close() !!}
                        </td>

                    <!-- Cas fonctionnel -->
                    @else
                        <td class="bg-success">{{ trans('texts.ok') }}</td>
                        <td>{{ $scale->id }}</td>
                        <td >
                            @if (count($owners) > 0)
                                @foreach ($owners as $owner)
                                    {!! '<strong>'. $owner->last_name . '</strong>' . ' ' . $owner->first_name !!}
                                @endforeach
                            @else
                                {!! '<div class="text-center"><strong>'. '-' .'</strong></div>' !!}
                            @endif
                        </td>
                        <td>{{ $scale->alias }}</td>
                        <td><a href="{{ route('scales.see', ["reference" => $scale->reference]) }}">{{ $scale->reference }}</a></td>
                        <td>{{ $scale->reports->count() }}</td>
                        <td>{{ $scale->imei }}</td>
                        <td>
                        {!! Form::open(['url' => route('scales.deleteScale', $scale->reference)]) !!}
                            {!! Form::submit('X', ['class' => 'delete btn btn-danger center-block',
                                    'onClick' => 'return confirm(\''.trans('texts.delete_balance_confirm').'\')'
                                ] ) 
                            !!}
                        {!! Form::close() !!}
                        </td>
                        <td>
                        {!! Form::open(['url' => route('scales.deleteAffectation', $scale->reference)]) !!}
                            {!! Form::submit('X', ['class' => 'delete btn btn-warning center-block',
                                'onClick' => 'return confirm(\''.trans('texts.delete_link_user_confirm').'\')'
                                ] ) 
                            !!}
                        {!! Form::close() !!}
                        </td>
                        <td>
                        {!! Form::open(['url' => route('scales.deleteReports', $scale->reference)]) !!}
                            {!! Form::submit('X', ['class' => 'delete btn btn-info center-block',
                                'onClick' => 'return confirm(\''.trans('texts.delete_statements_confirm').'\')'
                                ] ) 
                            !!}
                        {!! Form::close() !!}
                        </td>
                    @endif

                </tr>
            @endforeach
            </tbody>
            <span class="center">
                <!-- Pagination -->
                {!! $scales->appends(\Request::except('page'))->render() !!}
            </span>
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