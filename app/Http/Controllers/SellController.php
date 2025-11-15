<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SellController extends Controller
{
    public function sell(Request $request)
{
    $request->validate([
        'quantity' => 'required|integer|min:1',
        'selected_ids' => 'required|array|min:1',
        'selected_ids.*' => 'integer|exists:products,id',
    ]);

    $sellQty = $request->quantity;
    $selectedIds = $request->selected_ids;
    $totalPrice = 0;

    DB::transaction(function() use($sellQty, $selectedIds, &$totalPrice) {
        $items = Product::whereIn('id', $selectedIds)
                        ->orderBy('id', 'asc')
                        ->get();

        if ($items->isEmpty()) {
            throw new \Exception("No valid products selected");
        }

        foreach ($items as $item) {
            if ($sellQty <= 0) break;

            $take = min($item->quantity, $sellQty);

            Transaction::create([
                'item_id' => $item->id,
                'quantity' => $take,
                'price' => $item->price,
                'total_price' => $take * $item->price,
            ]);

            $item->quantity -= $take;
            $item->total_price = $item->quantity * $item->price;
            $item->save();

            $totalPrice += $take * $item->price;
            $sellQty -= $take;
        }
    });

    return response()->json(['success'=>true,'total_price'=>$totalPrice]);
}

}
