<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;

class PricesController extends Controller
{
    public function index()
    {
        return view('edit_prices', [
            'ingredients' => Ingredient::all()
        ]);
    }

    public function update(Request $request)
    {
        foreach ($request->ingredients as $id => $price) {
            $ingredient = Ingredient::findOrFail($id);

            $ingredient->update(['price_per_unit' => round($price, 2)]);
        }

        return back();
    }
}
