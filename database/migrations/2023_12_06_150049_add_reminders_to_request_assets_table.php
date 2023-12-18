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
        Schema::table('request_assets', function (Blueprint $table) {
            $table->foreignId('reminder_profile_id')->nullable();
            $table->date('reminder_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_assets', function (Blueprint $table) {
            $table->dropColumn('reminder_profile_id');
            $table->dropColumn('reminder_date');
        });
    }
};
