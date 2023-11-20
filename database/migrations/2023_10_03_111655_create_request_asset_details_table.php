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
        Schema::create('request_asset_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('file_id')->nullable(); 
            $table->unsignedBigInteger('request_asset_id'); 
            $table->unsignedTinyInteger('qty')->default(1)->nullable();
            $table->string('item_description');
            $table->string('asset_code')->nullable();
            $table->string('weight')->nullable();
            $table->decimal('item_value',10,2)->nullable();
            $table->string('reason_for_request')->nullable();
            $table->string('country_of_origin',120)->nullable(); // for public approval only
            $table->text('remarks',120)->nullable(); // for public approval only
            $table->unsignedTinyInteger('is_available')->default(0)->nullable();
            $table->unsignedTinyInteger('is_received')->default(0)->nullable();
            $table->unsignedTinyInteger('is_delivered')->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_asset_details');
    }
};
