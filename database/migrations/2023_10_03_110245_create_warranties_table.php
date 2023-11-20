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
        Schema::create('warranties', function (Blueprint $table) {
            $table->id();

            // warranty info
            $table->string('warranty_title', 150)->nullable();
            $table->date('warranty_start_date')->nullable();
            $table->date('warranty_end_date')->nullable();

            // Annual Maintenance Contract AMC info
            $table->foreignId('vendor_id')->nullable()->constrained(); // AMC Vendor
            $table->date('amc_start_date')->nullable();
            $table->date('amc_end_date')->nullable();

            // asset
            $table->foreignId('asset_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranties');
    }
};
