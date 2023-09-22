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

        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('status', 15)->nullable()->default('active');; // active, inactive
            $table->string('ecode', 25)->unique()->nullable();
            $table->string('username', 50)->nullable();
            $table->string('superior_ecode', 15)->nullable();
            $table->string('display_name', 150)->nullable();
            $table->string('first_name', 50)->nullable();
            $table->unsignedTinyInteger('grade')->nullable();
            $table->string('grade_original', 5)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->string('contact', 30)->nullable();
            $table->string('email', 80)->nullable(); 
            $table->string('department')->nullable();
            $table->string('designation')->nullable(); 
            $table->string('role')->nullable(); 
            $table->unsignedBigInteger('location_id')->default(null)->nullable();
            $table->foreignId('company_id')->constrained()->default(null)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
