<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('highlight');
            $table->string('image');
            $table->dateTime('start_event');
            $table->dateTime('end_event');
            $table->string('location');
            $table->string('status')->default('unpublished');
            $table->string('slug')->unique();
            $table->foreignId('event_category_id')->constrained('event_categories');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};