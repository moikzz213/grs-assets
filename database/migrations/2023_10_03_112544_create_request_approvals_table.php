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
        Schema::create('request_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_asset_id');
            $table->unsignedBigInteger('profile_id');
            $table->string('approval_type',50);
            $table->string('status',20)->default('pending');
            $table->date('date_approved')->nullable();
            $table->unsignedTinyInteger('orders')->default(0);
            $table->text('reason_rejected')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_approvals');
    }
};
