<?php

namespace App\Http\Requests;

use Illuminate\validation\Rule;

use App\Models\ProductSku;

class OrderRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //address_id   items    items.*.sku_id  items.*.amount
        return [
            'address_id' => ['required', Rule::exists('user_address','id')->where('user_id',$this->user()->id)],
            'items' => ['required','array'],
            'items.*.sku_id' => ['required', function($attribute,$value,$fail){
                    if (!$sku = ProductSku::find($value)) {
                        $fail('该商品不存在');  return;
                    }

                    if (!$sku->product->on_sale) {
                        $fail('该商品已售完');
                        return;
                    }

                    preg_match('/^items\.(/+d)\.sku_id/',$attribute,$m);
                    $index = $m[1];

                    $amount = $this->input('items')[$index]['amount'];
                    if ($amount > 0 && $amount > $sku->stock) {
                        $fail('该商品库存不足');
                        return;
                    }

            }],
            'items.*.amount' => ['required', 'integer', 'min:1'],
        ];
    }
}
