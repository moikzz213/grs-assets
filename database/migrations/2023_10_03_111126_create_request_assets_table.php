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
        Schema::create('request_assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_type_id');
            $table->string('types'); // transfer / request
            $table->unsignedBigInteger('transferred_from'); // location_id
            $table->unsignedBigInteger('transferred_to'); // location_id
            $table->date('date_closed')->nullable();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('profile_id');
            $table->string('subject',200);
            $table->string('status',20)->default('pending');
            $table->text('reason_rejected')->nullable();
            $table->unsignedTinyInteger('is_available')->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_assets');
    }
};
