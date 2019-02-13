<?php

namespace App\Exceptions;

use Exception;

use Illuminate\Http\Request;
class CouponCodeUnavailableException extends Exception
{
    public function __construct($meaasge, int $code = 403)
    {
    	parent::__construct($meaasge, $code);
    }

    public function render(Request $request)
    {
    	if ($request->expectsJson()) {
    		return response()->json(['msg' => $this->message], $this->code);
    	}

    	return redirect()->back()->withErrors(['coupon_code' => $this->message]);
    }
}
