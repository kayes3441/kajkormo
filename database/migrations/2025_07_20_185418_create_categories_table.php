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
        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->index();
            $table->enum('level',['category','subcategory','sub-subcategory']);
            $table->foreignUuid('parent_id')->nullable();
            $table->string('icon')->nullable();
            $table->integer('priority')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('parent_id')
                ->references('id')
                ->on('categories')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
