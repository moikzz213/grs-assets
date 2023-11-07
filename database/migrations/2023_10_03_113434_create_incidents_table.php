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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id')->nullable();
            $table->unsignedBigInteger('profile_id');
            $table->string('title',120);
            $table->text('description')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('handled_by')->nullable();
            $table->string('type_id',30); // complaint/ faults/ breakdown/ broken
            $table->string('priority',1)->nullable(); // 1 - High, 2 - Medium, 3 - Low
            $table->string('urgency',1); // 1 - High, 2 - Medium, 3 - Low
            $table->unsignedBigInteger('status_id')->nullable()->default(7);
            $table->date('date_closed')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
