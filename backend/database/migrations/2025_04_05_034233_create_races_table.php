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
        Schema::create('races', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->foreignId('racetrack_master_id')->constrained()->onDelete('cascade');
            $table->integer('race_num');
            $table->boolean('is_display_to_index')->default(false)->comment('レース一覧へ表示するか：true or false');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('races');
    }
};
