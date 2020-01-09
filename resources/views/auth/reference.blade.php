@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"> {{ trans('texts.check_ref_sacale') }}</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST"
                        action="{{ route('reference-verification') }}">
                        {{ csrf_field() }}
                        <!-- Reference -->
                        <div class="form-group{{ $errors->has('reference') ? ' has-error' : '' }}">
                            <label for="reference" class="col-md-4"> {{ trans('texts.your_scale_reference') }}</label>
                            <div class="col-md-8">
                                <input id="reference" type="reference" class="form-control" name="reference"
                                    value="{{ old('reference') }}" required placeholder="Entrez la référence qui se situe au dos de la balance">
                                    @if($errors->any())
                                    <span class="help-block">
                                        <strong style="color:#a94442;">{{ $errors->first() }}</strong>
                                    </span>
                                    @endif
                            </div>
                        </div>
                        <!-- Enregistrement -->
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Vérifier
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection