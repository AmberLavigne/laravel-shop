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
if (! function_exists('ngrok_url')) {
    function ngrok_url($routeName, $parameters = [])
    {
        // 开发环境，并且配置了 NGROK_URL
        if(app()->environment('local') && $url = config('app.ngrok_url')) {
            // route() 函数第三个参数代表是否绝对路径
            return $url.route($routeName, $parameters, false);
        }

        return route($routeName, $parameters);
    }
}
