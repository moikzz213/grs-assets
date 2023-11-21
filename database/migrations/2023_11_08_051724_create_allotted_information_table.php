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
        Schema::create('allotted_information', function (Blueprint $table) {
            $table->id();
            // $table->foreign('allotted_to_id')->nullable()->references('id')->on('locations');
            // $table->foreign('transferred_to_id')->nullable()->references('id')->on('locations');
            $table->unsignedBigInteger('allotted_to_id')->nullable();
            $table->unsignedBigInteger('transferred_to_id')->nullable();
            // $table->foreignId('transferred_to')->constrained();
            // $table->foreignId('allotted_to')->constrained();
            $table->foreignId('asset_id')->constrained();
            $table->string('type', 50)->nullable(); // allotted, transferred
            $table->string('remarks', 250)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allotted_information');
    }
};
