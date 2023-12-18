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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('title', 120);
            $table->string('address', 150)->nullable();
            $table->string('contact_no', 30)->nullable();
            $table->string('contact_name', 120)->nullable();
            $table->string('contact_email', 80)->nullable();
            $table->string('status', 15)->default('active'); 
            $table->unsignedBigInteger('profile_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
