<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;
use App\Models\ProductSku;
use App\Services\CartService;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    public function add(AddCartRequest $request)
    {
    	$this->cartService->add($requet->input('sku_id'),$request->input('amount'));

    	return [];
    }

    public function index(Request $request)
    {
    	$cartItems = $this->cartService->get();
        $addresses = $request->user()->addresses()->orderBy('last_used_at', 'desc')->get();

        return view('cart.index', ['cartItems' => $cartItems, 'addresses' => $addresses]);
    }

    public function remove(ProductSku $sku, Request $request)
    {
    	$request->user()->cartItems()->where('product_sku_id', $sku->id)->delete();
    }

    public function remove(ProductSku $productSku, Request $request)
    {
        $this->cartService->remove($productSku->id);
    }
}
