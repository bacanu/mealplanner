<?php

namespace Tests\Http\Controllers;

use App\Meal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MealsControllerTest extends TestCase
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
        $this->getJson(route('api.meals.index'))->assertUnauthorized();
        $this->get(route('meals.edit', 0), [])->assertRedirect(route('login'));

        $this->postJson(route('api.meals.store'), [])->assertUnauthorized();
        $this->deleteJson(route('api.meals.destroy', 0))->assertUnauthorized();
    }

    public function testIndex()
    {
        $meal = factory(Meal::class)->create();
        $response = $this->getJson(route('api.meals.index'));

        $response->assertStatus(200)
            ->assertExactJson([
                $meal->toArray(),
            ]);
    }

    public function testEdit()
    {
        $meal = factory(Meal::class)->create();
        $response = $this->get(route('meals.edit', $meal));

        $response->assertStatus(200);
    }

    public function testStore()
    {
        $response = $this->postJson(route('api.meals.store'), [
            'name' => 'New meal',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'name' => 'New meal',
            ]);
    }

    public function testDestroy()
    {
        $meal = factory(Meal::class)->create();

        $response = $this->deleteJson(route('api.meals.destroy', $meal));

        $response->assertStatus(200);

        $this->assertDatabaseMissing('meals', ['id' => $meal->id]);
    }
}
