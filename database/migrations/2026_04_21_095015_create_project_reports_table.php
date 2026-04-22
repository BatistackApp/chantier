<?php

use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Project::class)->constrained();
            $table->string('type');

            $table->boolean('supports_conformity')->default(false);
            $table->text('support_deviations')->nullable();
            $table->boolean('access_ok')->default(false);
            $table->boolean('electricity_ok')->default(false);

            $table->boolean('is_completed')->default(false);
            $table->boolean('cleaning_done')->default(false);
            $table->json('reserves')->nullable();

            $table->timestamp('signed_at')->nullable();
            $table->string('signatory_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_reports');
    }
};
