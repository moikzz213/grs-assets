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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            // $table->string('original_name', 150)->nullable(); // to remove
            $table->string('title', 150)->nullable();
            $table->string('disk', 150)->nullable();
            $table->string('path', 150)->nullable();
            $table->string('type', 50)->nullable(); // asset, request
            $table->string('mime', 80)->nullable();
            $table->unsignedBigInteger('profile_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
