<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Meal extends Model
{
    protected $fillable = [
        'name'
    ];

    protected $appends = [
        'action_urls',
        'has_ingredients_without_price'
    ];

    public static function make($name)
    {
        return self::create([
            'name' => $name
        ]);
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'ingredient_meal')
            ->withPivot(['quantity'])
            ->select(['ingredients.*', 'ingredient_meal.quantity']);
    }

    public function getHasIngredientsWithoutPriceAttribute() {
        return !! $this->ingredients()->where('price_per_unit', 0)->count();
    }

    public function addIngredient(Ingredient $ingredient, $quantity)
    {
        DB::table('ingredient_meal')
            ->insert([
                'meal_id' => $this->id,
                'ingredient_id' => $ingredient->id,
                'quantity' => $quantity
            ]);
    }

    public function getActionUrlsAttribute()
    {
        if (!$this->id) {
            return [];
        }
        
        return [
            'edit' => route('meals.edit', $this->id),
        ];
    }
}
