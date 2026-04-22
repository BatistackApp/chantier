<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectCost;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ProjectCostFactory extends Factory
{
    protected $model = ProjectCost::class;

    public function definition(): array
    {
        return [
            'cost_type' => $this->faker->word(),
            'label' => $this->faker->word(),
            'amount' => $this->faker->randomFloat(),
            'spent_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'project_id' => Project::factory(),
        ];
    }
}
