<?php

use App\Http\Controllers\DaysController;
use App\Http\Controllers\IngredientsController;
use App\Http\Controllers\MealsController;
use App\Http\Controllers\PlannerController;
use App\Http\Controllers\PricesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::group(['middleware' => ['web', 'auth']], function (){
    Route::get('/', [PlannerController::class, 'index']);
    Route::get('prices', [PricesController::class, 'index'])->name('prices.index');
    Route::put('prices', [PricesController::class, 'update'])->name('prices.update');
    Route::resource('meals', MealsController::class)
        ->only(['edit']);
    Route::resource('meals.ingredients', IngredientsController::class)
        ->only(['store', 'destroy']);
    Route::resource('api/days', DaysController::class)
        ->only(['index', 'update'])
        ->names('api.days');
    Route::resource('api/meals', MealsController::class)
        ->only(['index', 'store', 'destroy'])
        ->names('api.meals');
});

require __DIR__.'/auth.php';
