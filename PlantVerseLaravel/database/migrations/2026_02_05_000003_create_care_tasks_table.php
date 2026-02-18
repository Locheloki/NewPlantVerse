<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('care_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plant_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['Water', 'Sunlight', 'Fertilize']);
            $table->integer('frequency_days');
            $table->dateTime('last_completed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('care_tasks');
    }
};
