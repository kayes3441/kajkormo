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
        Schema::create('posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignUuid('category_id')->nullable();
            $table->foreignUuid('subcategory_id')->nullable();
            $table->foreignUuid('sub_subcategory_id')->nullable();
            $table->decimal('price', 10, 5)->default(0);
            $table->string('work_type')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('image')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
