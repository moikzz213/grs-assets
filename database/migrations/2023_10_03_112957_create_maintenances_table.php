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
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id');
            $table->string('service_type',50); 
            $table->string('status',50);
            $table->unsignedBigInteger('assignee')->nullable(); 
            $table->string('date_start',50)->nullable(); 
            $table->string('date_received',50)->nullable(); 
            $table->text('remarks')->nullable(); 
            $table->decimal('cost',10,2)->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
