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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('condition_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('model_id')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->unsignedBigInteger('author_id');
            $table->unsignedBigInteger('last_author_id')->nullable();
            $table->string('asset_name', 150);
            $table->string('asset_code', 50)->nullable()->unique();
            $table->string('serial_number', 80)->nullable();
            $table->string('section_code', 30)->nullable();
            $table->string('specification', 120)->nullable();
            $table->decimal('price', 10,2)->nullable();
            $table->string('po_number', 50)->nullable();
            $table->date('purchased_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
