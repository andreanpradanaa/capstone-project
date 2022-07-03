<?php

namespace Tests\Feature;

use App\Models\Food;
use App\Models\FoodCategory;
use App\Models\FoodDiary;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FoodTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $role = Role::factory()->create(['name' => 'admin', 'display_name' => 'Admin', 'description' => 'Admin']);
        $this->user = User::factory()->create();
        $this->user->roles()->attach($role);

        Sanctum::actingAs(
            $this->user,
            ['*']
        );

        $this->foodCategory = FoodCategory::factory()->create();
        $this->food = Food::factory()->create(['category_id' => $this->foodCategory->id]);
    }

    public function test_seach_food_by_name()
    {
        //preparation
//        $foodCategory = FoodCategory::factory()->create();
//        $food = Food::factory()->create(['category_id' => $foodCategory->id]);

        //action
        $response = $this->getJson(route('foods.search', ['search' => $this->food->name]));

        //assertion
        $this->assertEquals(1, $this->count($response->json()));
    }

    public function test_seach_food_by_id()
    {
        //preparation
//        $foodCategory = FoodCategory::factory()->create();
//        $food = Food::factory()->create(['category_id' => $foodCategory->id]);

        //action
        $response = $this->getJson(route('foods.search', ['food_id' => $this->food->id]));

        //assertion
        $this->assertEquals(1, $this->count($response->json()));
    }

    public function test_calculate_serving_size_of_food()
    {
        //preparation
//        $foodCategory = FoodCategory::factory()->create();
//        $food = Food::factory()->create(['category_id' => $foodCategory->id]);
        $servingSize = 200;

        //action
        $response = $this->getJson(route('foods.calculate', [$this->food->id, 'serving_size' => $servingSize]))
            ->assertOk()
            ->json();

        //assertion
        $this->assertEquals($this->food->air * $servingSize/100, $response[0]['air']);
    }

//    public function test_search_food_by_name_only_input_alphabet()
//    {
//        $foodCategory = FoodCategory::factory()->create();
//        $food = Food::factory()->create(['category_id' => $foodCategory->id]);
//
//        //action
//        $response = $this->getJson(route('foods.search', ['search' => null]));
//
//        //assertion
//        $response->assertSessionHasErrors([
//            "search" => "The search field is required when food id is not present."
//        ]);
//    }

    public function test_admin_fetch_all_food_data()
    {
        $response = $this->getJson(route('foods.index'))
            ->assertOk();

        $this->assertEquals(1, $this->count($response->json()));
    }

    public function test_admin_fetch_single_food_data()
    {
        $response = $this->getJson(route('foods.show', $this->food->id))
            ->assertOk()
            ->json();

        $this->assertEquals($response['name'], $this->food->name);
    }

    public function test_admin_store_new_food_data()
    {
        $food = Food::factory()->make();

        $response = $this->postJson(route('foods.store'), [
            'name' => $food->name,
            'sumber' => $food->sumber,
            'air' => $food->air,
            'energi' => $food->energi,
            'protein' => $food->protein,
            'lemak' => $food->lemak,
            'karbohidrat' => $food->karbohidrat,
            'serat' => $food->serat,
            'abu' => $food->abu,
            'kalsium' => $food->kalsium,
            'fosfor' => $food->fosfor,
            'besi' => $food->besi,
            'natrium' => $food->natrium,
            'kalium' => $food->kalium,
            'tembaga' => $food->tembaga,
            'seng' => $food->seng,
            'retinol' => $food->retinol,
            'b_karoten' => $food->b_karoten,
            'karoten_total' => $food->karoten_total,
            'thiamin' => $food->thiamin,
            'riboflamin' => $food->riboflamin,
            'niasin' => $food->niasin,
            'vitamin_c' => $food->vitamin_c,
            'porsi_berat_dapat_dimakan' => $food->porsi_berat_dapat_dimakan,
            'category_id' => $this->foodCategory->id,
        ])->assertCreated()
        ->json();

        $this->assertEquals($food->name, $response['name']);
        $this->assertDatabaseHas('foods', [
            'name' => $food->name,
            'sumber' => $food->sumber,
            'air' => $food->air,
            'energi' => $food->energi,
            'protein' => $food->protein,
            'lemak' => $food->lemak,
            'karbohidrat' => $food->karbohidrat,
            'serat' => $food->serat,
            'abu' => $food->abu,
            'kalsium' => $food->kalsium,
            'fosfor' => $food->fosfor,
            'besi' => $food->besi,
            'natrium' => $food->natrium,
            'kalium' => $food->kalium,
            'tembaga' => $food->tembaga,
            'seng' => $food->seng,
            'retinol' => $food->retinol,
            'b_karoten' => $food->b_karoten,
            'karoten_total' => $food->karoten_total,
            'thiamin' => $food->thiamin,
            'riboflamin' => $food->riboflamin,
            'niasin' => $food->niasin,
            'vitamin_c' => $food->vitamin_c,
            'porsi_berat_dapat_dimakan' => $food->porsi_berat_dapat_dimakan,
            'category_id' => $this->foodCategory->id,
        ]);
    }

    public function test_admin_update_food_data()
    {
        $this->patchJson(route('foods.update', $this->food->id), ['name' => 'updated food name'])
            ->assertOk();

        $this->assertDatabaseHas('foods', ['name' => 'updated food name']);
    }

    public function test_admin_destroy_food_data()
    {
        $this->deleteJson(route('foods.destroy', $this->food->id))
            ->assertNoContent();

        $this->assertDatabaseMissing('foods', ['name' => $this->food->id]);
    }
}
