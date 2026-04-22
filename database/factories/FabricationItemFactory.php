<?php

namespace Database\Factories;

use App\Models\Fabrication;
use App\Models\FabricationItem;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class FabricationItemFactory extends Factory
{
    protected $model = FabricationItem::class;

    public function definition(): array
    {
        return [
            'type' => $this->faker->word(),
            'label' => $this->faker->word(),
            'quantity' => $this->faker->randomFloat(),
            'unit_cost' => $this->faker->randomFloat(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'project_id' => Project::factory(),
            'fabrication_id' => Fabrication::factory(),
        ];
    }
}
