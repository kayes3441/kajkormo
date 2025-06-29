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
        Schema::create('carrier_gateways', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key')->index();
            $table->json('gateway_values');
            $table->json('additional_data')->nullable();
            $table->string('gateways_image')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrier_gateways');
    }
};
