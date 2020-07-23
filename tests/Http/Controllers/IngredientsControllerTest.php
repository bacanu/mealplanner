<?php

namespace Tests\Http\Controllers;

use App\Meal;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IngredientsControllerTest extends TestCase
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
        $meal = factory(Meal::class)->create();
        $this->post(route('meals.ingredients.store', $meal), [])->assertRedirect(route('login'));
        $this->delete(route('meals.ingredients.destroy', [$meal, 0]))->assertRedirect(route('login'));
    }


    public function testStore()
    {
        $ingredientName = $this->faker->name;

        $meal = factory(Meal::class)->create();
        $response = $this->post(route('meals.ingredients.store', $meal->id), [
            'name' => $ingredientName,
            'quantity' => 1,
        ]);

        $response->assertStatus(302);

        $this->assertTrue($meal->ingredients()->count() == 1, "Meal should have only 1 ingredient");
        $this->assertEquals(trim(ucfirst(strtolower($ingredientName))), $meal->ingredients[0]->name);
    }

    public function testDestroy()
    {
        $meal = factory(Meal::class)->state('with_varied_ingredients')->create();
        $ingredient = $meal->ingredients()->first();

        $this->assertDatabaseHas('ingredient_meal', ['meal_id' => $meal->id, 'ingredient_id' => $ingredient->id]);

        $response = $this->delete(route('meals.ingredients.destroy', [$meal, $ingredient]));

        $response->assertStatus(302);

        $this->assertDatabaseMissing('ingredient_meal', ['meal_id' => $meal->id, 'ingredient_id' => $ingredient->id]);
    }
}
