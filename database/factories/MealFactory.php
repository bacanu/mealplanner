<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Meal;
use Faker\Generator as Faker;

$factory->define(Meal::class, function (Faker $faker) {
    return [
        'name' => $faker->colorName,
    ];
});

$factory->define(\App\Ingredient::class, function (Faker $faker) {
    return [
        'name' => $faker->colorName,
        'price_per_unit' => $faker->numberBetween(1, 5000),
    ];
});

$factory->afterCreatingState(Meal::class, 'with_varied_ingredients', function ($model, Faker $faker) {
    $ingredients = [
        factory(\App\Ingredient::class)->create(),
        factory(\App\Ingredient::class)->create([
            'price_per_unit' => 0
        ]),
    ];

    foreach ($ingredients as $ingredient) {
        $model->addIngredient($ingredient, $faker->numberBetween(0, 2000));
    }
});

