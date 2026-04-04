<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create Plant Journals Table
 * 
 * Tracks the growth and care history of each plant.
 * Users can upload progress photos and add notes to document their plant's journey.
 * This creates a visual and narrative record of plant development over time.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plant_journals', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to plants table - delete journals when plant is deleted
            $table->foreignId('plant_id')->constrained()->onDelete('cascade');
            
            // Progress photo - nullable to allow text-only entries
            $table->string('photo_url')->nullable();
            
            // User's note about plant progress/observations
            $table->text('note')->nullable();
            
            // Timestamps for tracking when entry was created/updated
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plant_journals');
    }
};
