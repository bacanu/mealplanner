<?php

namespace Tests\Http\Controllers;

use App\Meal;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PricesControllerTest extends TestCase
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
        $this->get(route('prices.index'))->assertRedirect(route('login'));
        $this->put(route('prices.update'), [])->assertRedirect(route('login'));
    }

    public function testIndex()
    {
        $meal = factory(Meal::class)->state('with_varied_ingredients')->create();
        $ingredient = $meal->ingredients()->first();

        $result = $this->get(route('prices.index'))
            ->assertSeeText($ingredient->name);
    }

    public function testUpdate()
    {
        $meal = factory(Meal::class)->state('with_varied_ingredients')->create();
        $ingredient = $meal->ingredients()->first();

        $result = $this->put(route('prices.update'), ['ingredients' => [$ingredient->id => 1337]])
            ->assertStatus(302);

        $this->assertDatabaseHas('ingredients', ['id' => $ingredient->id, 'price_per_unit' => 1337]);
    }
}
