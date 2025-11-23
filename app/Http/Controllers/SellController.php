<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPurchase;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SellController extends Controller
{
    public function sell(Request $req)
{
    $req->validate([
        'items' => 'required|array',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
    ]);

    foreach ($req->items as $sellItem) {

        $product = Product::findOrFail($sellItem['product_id']);
        $sellQty = $sellItem['quantity'];

        // Check stock
        if ($sellQty > $product->quantity) {
            return response()->json([
                'message' => "Not enough stock for {$product->name}"
            ], 400);
        }

        // FIFO Lots
        $lots = ProductPurchase::where('product_id', $product->id)
            ->where('quantity', '>', 0)
            ->orderBy('id', 'asc')
            ->get();

        foreach ($lots as $lot) {

            if ($sellQty <= 0) break;

            $take = min($sellQty, $lot->quantity);

            // ↓↓↓ Save transaction ↓↓↓
            Transaction::create([
                'item_id' => $product->id,
                'lot_id' => $lot->id,
                'quantity' => $take,
                'price' => $lot->price,
                'total_price' => $take * $lot->price,
            ]);

            // reduce lot
            $lot->quantity -= $take;
            $lot->save();

            // reduce remaining sell quantity
            $sellQty -= $take;
        }

        // reduce main product stock
        $product->quantity -= $sellItem['quantity'];
        $product->save();
    }

    return response()->json(['message' => 'Products sold successfully']);
}



}
