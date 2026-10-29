<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_type_id')->constrained('meeting_types')->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('company')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->string('timezone')->default('UTC');
            $table->string('status')->default('confirmed');
            $table->string('cancellation_token', 64)->unique();
            $table->timestamps();

            $table->index(['meeting_type_id', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
