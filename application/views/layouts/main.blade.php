<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>ShowNotify - Keep it watching</title>
        <meta name="description" content="Simply sign up and add your favorite series, we'll do the rest.">
        <meta name="viewport" content="width=device-width">
		{{ HTML::style('assets/css/main.css') }}
		@yield('styles')
		{{ HTML::script('assets/js/vendor/modernizr-2.6.1.min.js' ) }}
		@yield('scripts')
    </head>
    <body>
    	<header id="header">
    		<div class="wrapper">
	            <h1 id="logo">ShowNotify</h1>
	            <nav id="nav">
	                <ul>
	                	<li><a href="{{ URL::to( '/' ) }}">Home</a></li>
	                	@if( Session::has( 'user') )
	                	<li><a href="{{ URL::to( '/series/index' ) }}">Search</a></li>
	                	<li><a href="{{ URL::to( '/logout/index' ) }}">Log out</a></li>
	                	@endif                                                     
	                </ul>
	                <a href="https://github.com/deviavir/shownotify" id="github"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_red_aa0000.png" alt="Fork me on GitHub"></a>            
	            </nav>              
	        </div>
        </header>
        <div id="container">
	        <div class="wrapper">
				{{$content}}
			</div>
		</div>
		<footer id="footer">
			<div class="wrapper">
				<ul>
					<li><a href="//thetvdb.com" target="_blank" title="thetvdb.com">thetvdb.com</a></li>
					<li><a href="//laravel.com" target="_blank" title="laravel.com">laravel.com</a></li>
					<li><a href="//h5bp.com" target="_blank" title="h5bp.com">h5bp.com</a></li>
				</ul>
			</div>
		</footer>
		{{ HTML::script('assets/js/plugins.js' ) }}
		{{ HTML::script('assets/js/main.js' ) }}
	</body>
</html>