<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>{{ trans('texts.app_name') }}</title>
	<link rel="shortcut icon" type="image/x-icon" href="{{ asset('icons/sbeeh.png') }}">

	<!-- Styles -->
	<link href="{{ asset('css/app.css') }}" rel="stylesheet">

	<style type="text/css">
		html, body {
			height: 100%;
		}

		.fill { 
			min-height: 100%;
		}

		.content {
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			text-align: center;
		}
	</style>

	<!-- Scripts -->
	<script>
		window.Laravel = {!! json_encode([
			'csrfToken' => csrf_token(),
		]) !!};
	</script>
</head>
<body>
	<div class="container fill">
			<div class="content">
				<a href="{{ route('home') }}">
					<img src="{{ asset('images/logo-sbeeh.jpg') }}">
				</a>
				<br>
				<h3>{{ trans('texts.site_on_construction') }}</h3>
			</div>
	</div>

	<script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
</body>
</html>