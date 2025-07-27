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
        Schema::create('otp_verification_codes', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->uuid('client_id')->nullable()->index();
            $table->string('channel', 20)->comment('sms,email');
            $table->string('context', 30)->comment('signup,login,2factor');
            $table->integer('code');
            $table->tinyInteger('attempts')->default(0);
            $table->tinyInteger('max_attempts')->default(5);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_verification_codes');
    }
};
