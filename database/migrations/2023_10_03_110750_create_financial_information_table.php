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
        Schema::create('financial_information', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id');
            $table->decimal('capitalization_price', 10,2)->nullable();
            $table->date('end_of_life')->nullable();
            $table->date('capitalization_date')->nullable();
            $table->string('depreciation_percentage', 20)->nullable();
            $table->decimal('scrap_value', 10,2)->nullable();
            $table->date('scrap_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_information');
    }
};
