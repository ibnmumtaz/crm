<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['call', 'email', 'meeting', 'note', 'proposal', 'other']);
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->string('outcome')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamp('editable_until')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['lead_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communications');
    }
};
