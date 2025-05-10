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
        Schema::create('horse_races', function (Blueprint $table) {
            $table->id();
            $table->foreignId('horse_id')->constrained()->onDelete('cascade');
            $table->foreignId('race_id')->constrained()->onDelete('cascade');
            $table->boolean('is_entry_confirmed')->default(true)->comment('出走確定したか：true or false');
            $table->unique(['horse_id', 'race_id'], 'unique_horse_race'); // 複合ユニーク
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horse_races');
    }
};
