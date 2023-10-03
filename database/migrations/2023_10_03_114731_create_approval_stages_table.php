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
        Schema::create('approval_stages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('approval_setup_id');

            /**
             *  asset-monitoring / commercial-manager / releasing / technical-operation / area-manager / operation-director /
             * receiver / ceo
             */
            
            $table->string('stages',50);
            $table->string('types',20); // requestor / verify / approve / confirm / receiver

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_stages');
    }
};
