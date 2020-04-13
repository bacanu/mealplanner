<?php

namespace App\Http\Controllers;

use App\Ingredient;
use App\Meal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IngredientsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
