    @extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <table class="col-xl-12 table table-bordered table-striped table-condensed">
            <thead>
                <th>{{ trans('texts.id') }}</th>
                <th>{{ trans('texts.topic') }}</th>
                <th>{{ trans('texts.created_at') }}</th>
                <th>{{ trans('texts.email') }}</th>
                <th>{{ trans('texts.description') }}</th>
                <th>{{ trans('texts.deleting_messages') }}</th>
            </thead>
            <tbody>
            @foreach ($messages as $message)
                <tr>
                    <td>{{ $message->id }}</td>
                    <td>{{ $message->subject }}</td>
                    <td>{{ $message->created_at }}</td>
                    <td><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></td>
                    <td>{{ $message->description }}</td>
                    <td>
                    @php //dd($message->id) @endphp
                    {!! Form::open(['url' => route('messages.deleteMessage', $message->id)]) !!}
                        {!! Form::submit('X', ['class' => 'delete btn btn-danger center-block',
                            'onClick' => 'return confirm(\''.trans('texts.confirm_deleting').'\')' 
                            ] ) 
                        !!}
                    {!! Form::close() !!}


                    </td>
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
