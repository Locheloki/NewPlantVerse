<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plants', function (Blueprint $table) {
            $table->index(['user_id', 'is_favorite', 'name'], 'plants_user_favorite_name_index');
            $table->index(['user_id', 'category', 'name'], 'plants_user_category_name_index');
        });

        Schema::table('care_tasks', function (Blueprint $table) {
            $table->index(['type', 'plant_id'], 'care_tasks_type_plant_index');
        });

        Schema::table('plant_journals', function (Blueprint $table) {
            $table->index(['plant_id', 'created_at'], 'plant_journals_plant_created_index');
        });
    }

    public function down(): void
    {
        Schema::table('plant_journals', function (Blueprint $table) {
            $table->dropIndex('plant_journals_plant_created_index');
        });

        Schema::table('care_tasks', function (Blueprint $table) {
            $table->dropIndex('care_tasks_type_plant_index');
        });

        Schema::table('plants', function (Blueprint $table) {
            $table->dropIndex('plants_user_category_name_index');
            $table->dropIndex('plants_user_favorite_name_index');
        });
    }
};
