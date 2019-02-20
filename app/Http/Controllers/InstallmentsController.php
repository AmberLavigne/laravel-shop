<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Installment;

class InstallmentsController extends Controller
{
    public function index(Request $request, Installment $installment)
    {
        $installments = $installment
            ->where('user_id', $request->user()->id)
            ->paginate(10);

        return view('installments.index', ['installments' => $installments]);
    }

    public function show(Installment $installment)
    {

        $this->authorize('view',$installment);
        $items = $installment->items()->orderBy('sequence')->get();

        return view('installments.show', [
            'installment' => $installment,
            'items'       => $items,
            'nextItem'    => $items->where('paid_at', null)->first(),
        ]);
    }
}
