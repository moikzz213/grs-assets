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
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('brand', 100)->nullable();
            $table->string('origin', 100)->nullable(); 
            $table->string('designation', 150)->nullable();
            $table->string('contact_no_2', 20)->nullable();
            $table->string('contact_email_2', 80)->nullable();
            $table->text('remarks')->nullable(); 
            $table->string('category',50)->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            //
        });
    }
};
