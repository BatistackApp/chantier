<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'reference' => $this->faker->word(),
            'address' => $this->faker->address(),
            'geo_lat' => $this->faker->latitude(),
            'geo_long' => $this->faker->word(),
            'status' => $this->faker->word(),
            'quoted_amount_cents' => $this->faker->randomNumber(),
            'estimated_cost_cents' => $this->faker->randomNumber(),
            'planned_start_date' => Carbon::now(),
            'planned_end_date' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'customer_id' => Customer::factory(),
        ];
    }
}
