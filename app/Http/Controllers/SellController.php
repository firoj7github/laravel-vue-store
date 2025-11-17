<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPurchase;
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

    DB::transaction(function () use (&$totalPrice, $sellQty, $selectedIds) {

        $products = Product::whereIn('id', $selectedIds)
            ->orderBy('id', 'asc')
            ->lockForUpdate()
            ->get();

        // Total stock check
        $totalStock = ProductPurchase::whereIn('product_id', $selectedIds)->sum('quantity');

        if ($sellQty > $totalStock) {
            throw new \Exception("Not enough stock! Only $totalStock available.");
        }

        foreach ($products as $product) {

            if ($sellQty <= 0) break;

            // FIFO LOTS
            $lots = ProductPurchase::where('product_id', $product->id)
                ->where('quantity', '>', 0)
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->get();

            foreach ($lots as $lot) {

                if ($sellQty <= 0) break;

                $take = min($lot->quantity, $sellQty);

                // Create sale transaction
                Transaction::create([
                    'item_id' => $product->id,
                    'lot_id' => $lot->id,
                    'quantity' => $take,
                    'price' => $lot->price, // REAL PURCHASE PRICE
                    'total_price' => $take * $lot->price,
                ]);

                // Deduct from LOT
                $lot->quantity -= $take;
                $lot->save();

                // Add to total
                $totalPrice += $take * $lot->price;
                $sellQty -= $take;
            }

            // Update product summary
            $this->updateSummary($product->id);
        }
    });

    return response()->json([
        'success' => true,
        'total_price' => $totalPrice,
    ]);
}


// PRODUCT SUMMARY UPDATE (very important)
private function updateSummary($id)
{
    // শুধু যেই LOT এ quantity > 0 আছে, সেই লটগুলো ধরবো
    $purchases = ProductPurchase::where('product_id', $id)
        ->where('quantity', '>', 0)
        ->get();

    $totalQty = $purchases->sum('quantity');

    $totalValue = $purchases->sum(function ($p) {
        return $p->quantity * $p->price;
    });

    $avgPrice = $totalQty > 0 ? $totalValue / $totalQty : 0;

    Product::where('id', $id)->update([
        'quantity' => $totalQty,
        'price' => $avgPrice,   
        'total_price' => $totalValue,
    ]);
}




}
