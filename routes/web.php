<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth']], function (){
    Route::get('/', 'PlannerController@index');
    Route::resource('prices', 'PricesController', ['only' => ['index', 'store']]);
    Route::resource('meals', 'MealsController', ['only' => ['index', 'edit']]);
    Route::resource('meals.ingredients', 'IngredientsController');
    Route::resource('api/days', 'DaysController')->names('api.days');
    Route::resource('api/meals', 'MealsController')->names('api.meals');
});


Auth::routes();
