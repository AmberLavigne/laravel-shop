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

if (! function_exists('big_number')) {
    function big_number($number, $scale = 2)
    {
        return  new \Moontoast\Math\BigNumber($number, $scale);
    }
}