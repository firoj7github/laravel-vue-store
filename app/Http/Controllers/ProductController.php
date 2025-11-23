<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return Product::with('purchases')->get()->map(function ($p) {

            return [
                'id' => $p->id,
                'name' => $p->name,
                'quantity' => $p->quantity,
                'price' => $p->price,

                // All product_purchases as LOTS
                'lots' => $p->purchases->map(function ($lot) {
                    return [
                        'id' => $lot->id,
                        'quantity' => $lot->quantity,
                        'price' => $lot->price
                    ];
                })
            ];
        });
    }
}
