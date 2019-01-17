<?php

if (! function_exists('sam')) {
	function sam ()
	{
		return 'ok';
	}
}

if (! function_exists('route_class')) {
	function route_class()
	{
		return str_replace('.' , '-', Route::CurrentRouteName());
	}
}