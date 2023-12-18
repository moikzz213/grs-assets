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
        Schema::create('asset_warranty', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained();
            $table->foreignId('warranty_id')->constrained()->references('id')->on('warranties')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_warranty');
    }
};
