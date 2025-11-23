<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductPurchase;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Product X
        $x = Product::create([
            'name' => 'X Item',
            'quantity' => 0,
            'price' => 0,
           ]);

        // Lot 1: 50 pcs × 5 tk
        ProductPurchase::create([
            'product_id' => $x->id,
            'quantity' => 50,
            'price' => 5,
            ]);

        // Lot 2: 50 pcs × 10 tk
        ProductPurchase::create([
            'product_id' => $x->id,
            'quantity' => 50,
            'price' => 10,
           ]);

        // Product Y
        $y = Product::create([
            'name' => 'Y Item',
            'quantity' => 0,
            'price' => 0,
            ]);

        // Lot 1: 200 pcs × 10 tk
        ProductPurchase::create([
            'product_id' => $y->id,
            'quantity' => 100,
            'price' => 10,
             ]);

        // Lot 2: 50 pcs × 10 tk
        ProductPurchase::create([
            'product_id' => $y->id,
            'quantity' => 100,
            'price' => 10,
           ]);


        // Auto Update Summary (for both products)
        $this->updateProductSummary($x->id);
        $this->updateProductSummary($y->id);
    }

    private function updateProductSummary($productId)
    {
        $purchases = ProductPurchase::where('product_id', $productId)->get();

        $totalQty = $purchases->sum('quantity');
        $totalValue = $purchases->sum(function ($p) {
            return $p->quantity * $p->price;
        });

        $avgPrice = $totalQty > 0 ? $totalValue / $totalQty : 0;

        Product::where('id', $productId)->update([
            'quantity' => $totalQty,
            'price' => $avgPrice,
        ]);
    }
}
