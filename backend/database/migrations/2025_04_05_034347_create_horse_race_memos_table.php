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
        Schema::create('horse_races_memos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('horse_id')->constrained()->onDelete('cascade');
            $table->foreignId('race_id')->constrained()->onDelete('cascade');
            $table->text('memo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horse_races_memos');
    }
};
