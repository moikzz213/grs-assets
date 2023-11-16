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
            // asset info
            $table->string('asset_name', 150);
            $table->string('asset_code', 50)->nullable()->unique();
            $table->string('section_code', 30)->nullable();
            $table->foreignId('company_id')->nullable()->constrained();
            $table->foreignId('category_id')->nullable()->constrained();
            $table->foreignId('location_id')->nullable()->constrained();
            $table->unsignedBigInteger('status_id'); //

            // additional info
            $table->unsignedBigInteger('condition_id');
            $table->foreignId('brand_id')->nullable()->constrained();
            $table->string('model_id', 120);
            $table->string('specification', 120)->nullable();
            $table->string('serial_number', 80)->nullable();

            // purchase info
            $table->foreignId('vendor_id')->nullable()->constrained();
            $table->string('po_number', 50)->nullable();
            $table->date('purchased_date')->nullable();
            $table->decimal('price', 10,2)->nullable();

            // financial info one-one
            // alloted info one-many
            // warranty info one-many
            // maintenance info one-many
            $table->unsignedBigInteger('author_id');
            $table->unsignedBigInteger('last_author_id')->nullable();
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
