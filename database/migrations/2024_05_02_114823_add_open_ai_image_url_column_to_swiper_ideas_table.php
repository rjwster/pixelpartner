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
            $table->string('open_ai_image_url', 600)->nullable()->after('image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('swiper_ideas', function (Blueprint $table) {
            $table->dropColumn('open_ai_image_url');
        });
    }
};
