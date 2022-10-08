<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Day extends Model
{
    protected $fillable = [
        'date',
    ];

    protected $dates = ['date'];

    protected $appends = ['action_urls', 'date_readable'];

    public static function make(Carbon $date)
    {
        $check = Day::where('date', $date)->first();

        if ($check) return $check;

        $day = self::create([
            'date' => $date
        ]);

        $slots = DaySlot::all();

        foreach ($slots as $slot) {
            DB::table('day_meal')
                ->insert([
                    'day_id' => $day->id,
                    'meal_id' => null,
                    'day_slot_id' => $slot->id
                ]);
        }

        return $day;
    }

    public static function populateInterval(Carbon $start, Carbon $end)
    {
        //FIXME
        $days = $start->diffInDays($end) + 1;

        for ($daysToAdd = 0; $daysToAdd < $days; $daysToAdd++) {
            $date = (new Carbon($start))->addDays($daysToAdd);

            Day::make($date);
        }
    }

    public static function getOrMakeInterval($start, $end)
    {
        $days = Day::whereBetween('date', [$start, $end])->get();

        if (count($days) == 14) {
            return $days;
        }

        Day::populateInterval($start, $end);
        $days = Day::whereBetween('date', [$start, $end])->get();

        return $days;
    }

    public function assignMealToSlot(Meal $meal, DaySlot $slot)
    {
        return DB::table('day_meal')
            ->where('day_id', $this->id)
            ->where('day_slot_id', $slot->id)
            ->update(['meal_id' => $meal->id]);
    }

    public function clearSlot(DaySlot $slot)
    {
        return DB::table('day_meal')
            ->where('day_id', $this->id)
            ->where('day_slot_id', $slot->id)
            ->update(['meal_id' => null]);
    }

    public function meals()
    {
        return $this->belongsToMany(Meal::class, 'day_meal');
    }

    public function daySlots()
    {
        return $this->belongsToMany(DaySlot::class, 'day_meal')->withPivot(['meal_id']);
    }

    public function getMeals()
    {
        $meals = [];

        foreach ($this->daySlots as $slot) {
            if ($slot->pivot->meal_id) {
                $toAdd = Meal::findOrFail($slot->pivot->meal_id);
            } else {
                $toAdd = new Meal();
                $toAdd->name = '';
            }

            $toAdd->day_slot_id = $slot->id;

            $meals[] = $toAdd;
        }

        return $meals;
    }

    public function getActionUrlsAttribute()
    {
        if (!$this->id) {
            return [];
        }

        return [
            'update' => route('api.days.update', $this->id),
        ];
    }

    public function getDateReadableAttribute()
    {
        return $this->date->format('l d-m');
    }

}
