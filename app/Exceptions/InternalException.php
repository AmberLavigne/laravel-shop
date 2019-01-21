<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
class InternalException extends Exception
{
    #系统内部异常
    #
    protected $msgForUser;

    public function __construct(string $message, string $msgForUser = '系统内部错误', int $code = 500)
    {
    	parent::__construct();
    	$this->msgForUser = msgForUser;

    }

    public function render(Request $request){

    	if (! $request->expectsJson()) {
    		return request()->json(['msg' => $this->msgForUser ], $this->code);
    	}

    	return view('pages.error', ['msg' => $this->msgForUser]);
    }
}
