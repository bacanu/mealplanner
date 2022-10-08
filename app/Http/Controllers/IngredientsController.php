<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Meal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IngredientsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $mealId)
    {
        /** @var Meal $meal */
        $meal = Meal::findOrFail($mealId);

        $ingredient = Ingredient::make($request->name);

        $meal->addIngredient($ingredient, $request->quantity);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($mealId, $id)
    {
        /** @var Meal $meal */
        $meal = Meal::findOrFail($mealId);

        $ingredient = Ingredient::findOrFail($id);

        DB::table('ingredient_meal')
            ->where('meal_id', $meal->id)
            ->where('ingredient_id', $ingredient->id)
            ->delete();

        return redirect()->back();
    }
}
