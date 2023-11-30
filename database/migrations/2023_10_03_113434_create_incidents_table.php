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
            $table->foreignId('profile_id')->constrained();
            $table->string('title',120);
            $table->string('asset_code',120)->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('handled_by')->nullable();
            $table->string('priority',1)->nullable(); // 1 - High, 2 - Medium, 3 - Low
            $table->unsignedBigInteger('urgency_id')->nullable(); // urgency_id is status_id
            $table->unsignedBigInteger('type_id')->nullable(); // updated // complaint/ faults/ breakdown/ broken
            $table->unsignedBigInteger('status_id')->nullable()->default(7);
            $table->date('date_closed')->nullable();
            $table->date('reminder_date')->nullable();
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
