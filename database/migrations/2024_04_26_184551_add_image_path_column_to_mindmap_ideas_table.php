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
        Schema::table('mindmap_ideas', function (Blueprint $table) {
            $table->string('image_path', 500)->nullable()->after('idea_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mindmap_ideas', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
