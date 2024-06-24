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
        Schema::create('mindmap_ideas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('mindmap_ideas');

            $table->unsignedBigInteger('mindmap_id');
            $table->foreign('mindmap_id')->references('id')->on('mindmaps');

            $table->string('idea');

            $table->enum('idea_type', ['user', 'ai'])->default('user');

            $table->string('loc')->default('0 0');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mindmap_ideas');
    }
};
