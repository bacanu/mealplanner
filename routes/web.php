<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth']], function (){
    Route::get('/', 'PlannerController@index');
    Route::get('prices', 'PricesController@index')->name('prices.index');
    Route::put('prices', 'PricesController@update')->name('prices.update');
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
