<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAddress;
use App\Http\Requests\UserAddressRequest;

class UserAddressesController extends Controller
{
	/**
	 *获取地址信息all
	 * 
	 * [index description]
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
    public function index(Request $request)
    {
    	return view('user_addresses.index', [
            'addresses' => $request->user()->addresses,
        ]);
    }

    public function create()
    {
    	return view('user_addresses.create_and_edit', ['address' => new UserAddress()]);
    }
    /**
     * 保存地址
     * 
     * @param  UserAddressRequest $request [description]
     * @return [type]                      [description]
     */
    public function store(UserAddressRequest $request)
    {
        $request->user()->addresses()->create($request->only([
            'province',
            'city',
            'district',
            'address',
            'zip',
            'contact_name',
            'contact_phone',
        ]));

        return redirect()->route('user_addresses.index');
    }

    public function edit(UserAddress $user_address)
    {
    	$this->authorize('own',$user_address);
        return view('user_addresses.create_and_edit', ['address' => $user_address]);
    }

    public function update(UserAddress $user_address, UserAddressRequest $request)
    {
    	$this->authorize('own',$user_address);

        $user_address->update($request->only([
            'province',
            'city',
            'district',
            'address',
            'zip',
            'contact_name',
            'contact_phone',
        ]));

        return redirect()->route('user_addresses.index');
    }

    public function destroy(UserAddress $user_address)
    {
    	$this->authorize('own', user_address);
    	$user_address->delete();

    	return response()->json(['msg' => 'succes']);
    }
}
