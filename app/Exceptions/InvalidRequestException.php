<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class InvalidRequestException extends Exception
{
    public function __construct(string $message = '', int $code = 400){
    	parent::__contruct($message, $code);
    }

    public function render()
    {
    	# render 渲染
    	if (! $request->expectsJson()) {
    		return response)->json(['msg' => $this->message], $this->code);
    	}

    	return view('pages.error', ['msg' => $this->message]);
    }
}