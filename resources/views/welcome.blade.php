@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading"> {{ trans('texts.network_coverage') }} <i>(via <a href="https://www.quechoisir.org/carte-interactive-reseau-mobile-n21247/">quechoisir.org</a>)</i></div>
                    <div class="panel-body" id="networkCoverage">
                        <table width="100%">
                            <tbody>
                                <tr>
                                    <td align="center">
                                        <iframe id="map" scrolling="yes" width="955px" height="662" frameborder="0" src="https://info-reseau.quechoisir.org/index2.php"></iframe>
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
        (function () {
            var width = document.getElementById('map').offsetWidth;
            document.getElementById('map').width = width;
        })();
    </script>
@endsection
