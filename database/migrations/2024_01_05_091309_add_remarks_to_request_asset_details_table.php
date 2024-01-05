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
        Schema::table('request_asset_details', function (Blueprint $table) {
            $table->string('remarks_commercial', 150)->nullable();
            $table->string('remarks_receive', 150)->nullable();
            $table->string('remarks_release', 150)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_asset_details', function (Blueprint $table) {
            $table->dropColumn('remarks_commercial');
            $table->dropColumn('remarks_receive');
            $table->dropColumn('remarks_release');
        });
    }
};
