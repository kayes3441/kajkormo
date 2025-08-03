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
        Schema::create('locatables', function (Blueprint $table) {
            $table->id();
            $table->uuid('locatable_id');
            $table->string('locatable_type');
            $table->string('level');
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->index(['locatable_id', 'locatable_type', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locatables');
    }
};
