<?php

namespace App\Http\Controllers;

use App\Ingredient;
use Illuminate\Http\Request;

class PricesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
