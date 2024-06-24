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
        Schema::table('swiper_ideas', function (Blueprint $table) {
            $table->text('revised_prompt')->nullable()->after('image_prompt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('swiper_ideas', function (Blueprint $table) {
            $table->dropColumn('revised_prompt');
        });
    }
};
