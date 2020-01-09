@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <!-- VIA QUECHOISIR.ORG : https://www.quechoisir.org/carte-interactive-reseau-mobile-n21247/ -->
                    <div class="panel-heading"> {{ trans('texts.network_coverage') }}</div>
                    <div class="panel-body" id="networkCoverage">
                        <table width="100%">
                            <tbody>
                                <tr>
                                    <td align="center">
                                        {{-- <iframe id="map" scrolling="yes" width="955px" height="662" frameborder="0" src="https://info-reseau.quechoisir.org/index2.php"></iframe> --}}
                                        <iframe id="map" scrolling="yes" frameborder="0" src="https://www.monreseaumobile.fr/"></iframe>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        $('#map').width($('#networkCoverage').width());
        $('#map').height(800);
    });
</script>
@endsection
