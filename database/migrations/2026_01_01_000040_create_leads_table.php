<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->foreignId('pipeline_id')->constrained('pipelines')->restrictOnDelete();
            $table->foreignId('stage_id')->constrained('pipeline_stages')->restrictOnDelete();
            $table->foreignId('source_id')->nullable()->constrained('lead_sources')->nullOnDelete();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->decimal('value', 15, 2)->default(0);
            $table->char('currency', 3)->default('USD');
            $table->date('expected_close')->nullable();
            $table->enum('status', ['active', 'won', 'lost', 'archived'])->default('active');
            $table->string('lost_reason')->nullable();
            $table->timestamp('last_contact_at')->nullable();
            $table->timestamp('stalled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('owner_id');
            $table->index('team_id');
            $table->index('stage_id');
            $table->index('last_contact_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
