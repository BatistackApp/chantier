<?php

use App\Enums\ProjectStatus;
use App\Models\Customer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Customer::class)->constrained();
            $table->string('title');
            $table->string('reference')->unique();
            $table->string('address');
            $table->string('geo_lat')->nullable();
            $table->string('geo_long')->nullable();
            $table->string('status')->default(ProjectStatus::DRAFT->value);
            $table->decimal('quoted_amount', 14, 2)->default(0);
            $table->decimal('estimated_cost', 14, 2)->default(0);
            $table->date('planned_start_date')->nullable();
            $table->date('planned_end_date')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
