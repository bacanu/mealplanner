<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = [
        'name',
        'price_per_unit'
    ];

    public static function make($name)
    {
        $name = trim(ucfirst(strtolower($name)));

        $check = Ingredient::where('name', $name)->first();

        if ($check) return $check;

        $self = self::create([
            'name' => $name,
        ]);

        return $self;
    }
}
