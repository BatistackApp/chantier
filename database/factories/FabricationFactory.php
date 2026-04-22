<?php

namespace Database\Factories;

use App\Models\Fabrication;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class FabricationFactory extends Factory
{
    protected $model = Fabrication::class;

    public function definition(): array
    {
        return [
            'type' => $this->faker->word(),
            'label' => $this->faker->word(),
            'dimensions' => $this->faker->word(),
            'quantity' => $this->faker->randomFloat(),
            'color_code' => $this->faker->word(),
            'time_realized' => Carbon::now(),
            'unit_cost' => $this->faker->randomFloat(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'project_id' => Project::factory(),
        ];
    }
}
