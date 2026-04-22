<?php

use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fabrications', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Project::class)->constrained();
            $table->string('type'); // Enum FabricationType (Pliage, etc)
            $table->string('label'); // Désignation (ex: Pliage R1 DEV515)
            $table->string('dimensions')->nullable(); // Dimension (ex: 3ml)
            $table->decimal('quantity', 10, 2);
            $table->string('color_code')->nullable(); // Couleur (ex: 1015, 7016)
            $table->time('time_realized')->nullable(); // Temps réalisé

            $table->decimal('unit_cost')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fabrications');
    }
};
