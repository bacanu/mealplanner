<?php

namespace Tests\Feature;

use App\Meal;
use App\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(\App\User::class)->create();
    }

    public function testMealsAreListed()
    {
        $this->actingAs($this->user);

        $meal = factory(Meal::class)->create();
        $response = $this->get(route('api.meals.index'));

        $response->assertStatus(200)
            ->assertExactJson([
                $meal->toArray(),
            ]);
    }

    public function testMealCanBeCreated()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('api.meals.store'), [
            'name' => 'New meal',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'name' => 'New meal',
            ]);
    }

    public function testIngredientCanBeAddedToExistingMeal()
    {
        $this->actingAs($this->user);

        $faker = Factory::create();
        $ingredientName = $faker->name;

        $meal = factory(Meal::class)->create();
        $response = $this->post(route('meals.ingredients.store', $meal->id), [
            'name' => $ingredientName,
            'quantity' => 1,
        ]);

        $response->assertStatus(302);

        $this->assertTrue($meal->ingredients()->count() == 1, "Meal should have only 1 ingredient");
        $this->assertEquals(trim(ucfirst(strtolower($ingredientName))), $meal->ingredients[0]->name);
    }

    public function testIngredientCanBeRemovedFromMeal()
    {
        $this->actingAs($this->user);

        $meal = factory(Meal::class)->state('with_varied_ingredients')->create();
        $ingredient = $meal->ingredients()->first();

        $this->assertDatabaseHas('ingredient_meal', ['meal_id' => $meal->id, 'ingredient_id' => $ingredient->id]);

        $response = $this->delete(route('meals.ingredients.destroy', [$meal, $ingredient]));

        $response->assertStatus(302);

        $this->assertDatabaseMissing('ingredient_meal', ['meal_id' => $meal->id, 'ingredient_id' => $ingredient->id]);

    }

    //list day slots
    //bulk assign meals to day slots

    //generate ingredient list
}
