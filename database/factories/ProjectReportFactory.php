<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectReport;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ProjectReportFactory extends Factory
{
    protected $model = ProjectReport::class;

    public function definition(): array
    {
        return [
            'type' => $this->faker->word(),
            'supports_conformity' => $this->faker->boolean(),
            'support_deviations' => $this->faker->word(),
            'access_ok' => $this->faker->boolean(),
            'electricity_ok' => $this->faker->boolean(),
            'is_completed' => $this->faker->boolean(),
            'cleaning_done' => $this->faker->boolean(),
            'reserves' => $this->faker->words(),
            'signed_at' => Carbon::now(),
            'signatory_name' => $this->faker->name(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'project_id' => Project::factory(),
        ];
    }
}
