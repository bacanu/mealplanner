<?php

namespace App\Http\Controllers;

use App\Day;
use App\DaySlot;
use App\Meal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DaysController extends Controller
{
    public function main()
    {
        return view('mealplanner');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $start = Carbon::now()->startOfWeek();
        $end = Carbon::now()->addWeek()->endOfWeek();
        $days = Day::getOrMakeInterval($start, $end);

        foreach ($days as $day) {
            $day->meals = $day->getMeals();
            $day->isToday = $day->date == Carbon::now()->startOfDay();
        }

        $lastFilledDays = $this->getLastFilledWeek();

        $slots = DaySlot::orderBy('order', 'asc')->get();

        $ingredients = [];

        $prepStart = Carbon::now()->addDays(7)->startOfWeek();
        $prepEnd = Carbon::now()->addDays(7)->endOfWeek();
        $prepDays = Day::getOrMakeInterval($prepStart, $prepEnd);

        foreach ($prepDays as $day) {
            foreach ($day->getMeals() as $meal) {
                foreach ($meal->ingredients as $ingredient) {
                    if (isset($ingredients[$ingredient->name])) {
                        $ingredients[$ingredient->name]->quantity += $ingredient->quantity;
                    } else {
                        $ingredients[$ingredient->name] = $ingredient;
                    }
                }
            }
        }

        $prepStart = $prepStart->format('d-m-Y');
        $prepEnd = $prepEnd->format('d-m-Y');

        return compact('days', 'slots', 'ingredients', 'prepStart', 'prepEnd', 'lastFilledDays');
    }

    private function getLastFilledWeek()
    {
        //this assumes that the last day will be on a sunday
        $lastDay = Day::join('day_meal', 'day_meal.day_id', '=', 'days.id')
            ->whereNotNull('day_meal.meal_id')
            ->orderBy('days.date', 'desc')->first();

        if (!$lastDay) {
            return [];
        }

        $start = $lastDay->date->startOfWeek();
        $end = $lastDay->date->endOfWeek();
        $days = Day::getOrMakeInterval($start, $end);

        foreach ($days as $day) {
            $day->meals = $day->getMeals();
            $day->isToday = $day->date == Carbon::now()->startOfDay();
        }

        return $days;
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        /** @var Day $day */
        $day = Day::findOrFail($id);

        foreach ($request->meals as $m) {
            $slot = DaySlot::findOrFail($m['day_slot_id']);

            if (!isset($m['id']) || !$m['id']) {
                $day->clearSlot($slot);
            } else {
                $meal = Meal::findOrFail($m['id']);
                $day->assignMealToSlot($meal, $slot);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
