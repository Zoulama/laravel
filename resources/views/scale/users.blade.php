@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <table class="col-xl-12 table table-bordered table-striped table-condensed">
            <thead>
                <th>{{ trans('texts.id') }}</th>
                <th>{{ trans('texts.access_scale_owner') }}</th>. 
                <th>{{ trans('texts.email') }}</th>
                <th>{{ trans('texts.phone_number') }}</th>
                <th>{{ trans('texts.creation') }}</th>
                <th>M{{ trans('texts.to_update') }}</th>
                <th>{{ trans('texts.number_of_scales') }}</th>
                <th>{{ trans('texts.user_delete') }}</th>
            </thead>
            <tbody>
            @foreach ($users as $user)
                @php $nbOfscales = 0 ; @endphp
                @for($i = 0; $i < count($scale_user_table); $i++)
                    <!-- Si id de la table de liaison des balances est égal à celui de l'utilisateur -->
                    @if($scale_user_table[$i]->user_id === $user->id)
                        @php $nbOfscales++ @endphp
                    @endif
                @endfor
                <tr>
                    @if(!is_null($user->id))
                        <td>{{ $user->id }}</td>
                        <td>
                            @if($nbOfscales === 0 && $user->id != 1)
                                {!! '<strong>'. $user->last_name . '</strong>' . ' ' . $user->first_name !!}
                            @else
                                <!-- Si administrateur -->
                                @if($user->id === 1)
                                    <a href="{{ route('users.scaleByUser', $user) }}" target="_blank">{!! '<strong>'. $user->last_name . '</strong>' . ' ' . $user->first_name !!} </a>
                                @else
                                    <a href="{{ route('users.scaleByUser', $user) }}" target="_blank">{!! '<strong>'. $user->last_name . '</strong>' . ' ' . $user->first_name !!} </a>
                                @endif    
                                                        
                            @endif    
                        </td>
                        <td><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
                        <td>{{ $user->phone_number }}</td>
                        <td>{{ $user->created_at }}</td>
                        <td>{{ $user->updated_at }}</td>
                        <td>
                            <!-- Si pas de balances pour -->
                            @if($nbOfscales === 0)
                                @if($user->id !== 1)
                                    {!! '<div class="text-center"><strong>'. '-' .'</strong></div>' !!} 
                                @endif
                            @else
                                {!! '<div class="text-center"><strong>'. $nbOfscales .'</strong></div>' !!} 
                            @endif
                            @if($user->id === 1)
                                {!! '<div class="text-center"><strong>'. (string) (count($scalesAdmin)) .'</strong></div>' !!} 
                            @endif
                        <td>
                        {!! Form::open(['url' => route('users.deleteUser', $user->id)]) !!}
                            {!! Form::submit('X', ['class' => 'delete btn btn-danger center-block',
                                'onClick' => 'return confirm(\''.trans('texts.delete_user_confirm').'\')' 
                                ] ) 
                            !!}
                        {!! Form::close() !!}
                        </td>
                    @endif
                <tr>
            @endforeach
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
