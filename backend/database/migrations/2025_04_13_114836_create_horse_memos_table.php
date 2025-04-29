<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('horse_memos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('horse_id')->unique();
            $table->text('memo')->nullable();
            $table->timestamps();

            $table->foreign('horse_id')->references('id')->on('horses')->onDelete('cascade');
        });;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horse_memos');
    }
};
