<?php

use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_preparations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Project::class)->unique()->constrained()->cascadeOnDelete();

            $table->boolean('subcontractor_form_ok')->default(false);
            $table->boolean('subcontractor_contract_ok')->default(false);

            $table->json('logistics_status')->nullable();
            $table->string('lifting_means')->nullable();
            $table->integer('lifting_count')->default(0);
            $table->string('lifting_provider')->nullable();

            $table->boolean('safety_nets_required')->default(false);
            $table->string('safety_nets_provider')->nullable();

            $table->longText('observations')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_preparations');
    }
};
