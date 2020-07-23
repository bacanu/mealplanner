<?php

namespace Tests\Http\Controllers;

use App\Day;
use App\Meal;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DaysControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(\App\User::class)->create();
        $this->actingAs($this->user);
    }

    public function testControllerRequiresAuth()
    {
        $this->app['auth']->logout();
        $this->getJson(route('api.days.index'), [])->assertUnauthorized();
        $this->putJson(route('api.days.update', 0), [])->assertUnauthorized();
    }

    public function testIndex()
    {
        $response = $this->getJson(route('api.days.index'));

        $response->assertStatus(200);
    }

    public function testIndexHasExactly_2WeeksOfDays()
    {
        $response = $this->getJson(route('api.days.index'));

        $response->assertJsonCount(14, 'days');
    }

    public function testUpdateAssignsMealsToTheDaySlotsAndClearsThem()
    {
        $meal = factory(Meal::class)->state('with_varied_ingredients')->create();

        $day = Day::make(Carbon::now());
        $slot = $day->daySlots()->firstOrFail();

        $response = $this->putJson(route('api.days.update', $day), [
            'meals' => [
                [
                    'day_slot_id' => $slot->id,
                    'id' => $meal->id,
                ],
                [
                    'day_slot_id' => $slot->id + 1,
                    'id' => null,
                ],
                [
                    'day_slot_id' => $slot->id + 2,
                    'id' => $meal->id,
                ],
            ],
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('day_meal', ['day_id' => $day->id, 'meal_id' => $meal->id, 'day_slot_id' => $slot->id]);
        $this->assertDatabaseHas('day_meal', ['day_id' => $day->id, 'meal_id' => null, 'day_slot_id' => $slot->id + 1]);
        $this->assertDatabaseHas('day_meal', ['day_id' => $day->id, 'meal_id' => $meal->id, 'day_slot_id' => $slot->id + 2]);

    }
}
