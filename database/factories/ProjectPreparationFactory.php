<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectPreparation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ProjectPreparationFactory extends Factory
{
    protected $model = ProjectPreparation::class;

    public function definition(): array
    {
        return [
            'subcontractor_form_ok' => $this->faker->boolean(),
            'subconstractor_contract_ok' => $this->faker->boolean(),
            'logistics_status' => $this->faker->words(),
            'lifting_means' => $this->faker->word(),
            'lifting_count' => $this->faker->randomNumber(),
            'lifting_provider' => $this->faker->word(),
            'safety_nets_required' => $this->faker->boolean(),
            'safety_nets_provider' => $this->faker->word(),
            'observations' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'project_id' => Project::factory(),
        ];
    }
}
