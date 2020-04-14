<?php

namespace App\Http\Controllers;

use App\Day;
use App\DaySlot;
use App\Meal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DaysController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $days = $this->getDaysToDisplay();
        $lastFilledDays = $this->getLatestPlannedWeek();
        $slots = $this->getSlots();
        $ingredients = $this->getIngredientsForNextWeek();

        $prepStartReadable = Carbon::now()->addDays(7)->startOfWeek()->format('d-m-Y');
        $prepEndReadable = Carbon::now()->addDays(7)->endOfWeek()->format('d-m-Y');

        return compact('days', 'slots', 'ingredients', 'prepStartReadable', 'prepEndReadable', 'lastFilledDays');
    }

    /**
     * 2 weeks: the current one and the next one
     */
    private function getDaysToDisplay() {
        $start = Carbon::now()->startOfWeek();
        $end = Carbon::now()->addWeek()->endOfWeek();
        $days = Day::getOrMakeInterval($start, $end);

        foreach ($days as $day) {
            $day->meals = $day->getMeals();
            $day->isToday = $day->date == Carbon::now()->startOfDay();
        }

        return $days;
    }

    /**
     * Breakfast / Lunch / Dinner
     * Stored in the database so they can be changed easily
     */
    private function getSlots() {
        $slots = DaySlot::orderBy('order', 'asc')->get();

        return $slots;
    }

    private function getIngredientsForNextWeek() {
        $ingredients = [];

        $prepStart = Carbon::now()->addDays(7)->startOfWeek();
        $prepEnd = Carbon::now()->addDays(7)->endOfWeek();
        $prepDays = Day::getOrMakeInterval($prepStart, $prepEnd);

        foreach ($prepDays as $day) {
            foreach ($day->getMeals() as $meal) {
                foreach ($meal->ingredients as $ingredient) {
                    if (isset($ingredients[$ingredient->name])) {
                        $ingredients[$ingredient->name]->quantity = round(($ingredients[$ingredient->name]->quantity + $ingredient->quantity), 2);
                    } else {
                        $ingredients[$ingredient->name] = $ingredient;
                    }
                }
            }
        }

        return $ingredients;
    }

    /**
     * Used to populate empty weeks with the latest / most recent planned week
     * Useful if the planner hasn't been used for some time
     */
    private function getLatestPlannedWeek()
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

        foreach ($request->meals as $meal) {
            $slot = DaySlot::findOrFail($meal['day_slot_id']);

            if (!isset($meal['id']) || !$meal['id']) {
                $day->clearSlot($slot);
            } else {
                $m = Meal::findOrFail($meal['id']);
                $day->assignMealToSlot($m, $slot);
            }
        }
    }
}
