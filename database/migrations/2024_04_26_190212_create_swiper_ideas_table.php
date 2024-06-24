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
        Schema::create('swiper_ideas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('mindmap_id');
            $table->foreign('mindmap_id')->references('id')->on('mindmaps');

            $table->integer('group_number');
            $table->string('idea', 100);
            
            $table->text('image_prompt');
            $table->string('image_path', 500)->nullable();

            // 0 = not accepted, 1 = accepted
            $table->boolean('accepted')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('swiper_ideas');
    }
};
