<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth']], function (){
    Route::get('/', 'PlannerController@index');
    Route::resource('prices', 'PricesController')
        ->only(['index', 'store']);
    Route::resource('meals', 'MealsController')
        ->only(['edit']);
    Route::resource('meals.ingredients', 'IngredientsController')
        ->only(['store', 'destroy']);
    Route::resource('api/days', 'DaysController')
        ->only(['index', 'update'])
        ->names('api.days');
    Route::resource('api/meals', 'MealsController')
        ->only(['index', 'store', 'destroy'])
        ->names('api.meals');
});


Auth::routes();
