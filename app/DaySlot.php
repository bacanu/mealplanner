<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DaySlot extends Model
{
    protected $fillable = [
        'name',
        'order',
    ];

    public static function make($name, $order)
    {
        self::create([
            'name' => $name,
            'order' => $order,
        ]);
    }
}
