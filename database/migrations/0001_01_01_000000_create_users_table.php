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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->text('image')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->enum('gender', ['male', 'female' ,'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password');
            $table->uuid('temporary_token')->nullable();
            $table->char('device_token',80)->nullable();
            $table->tinyInteger('login_attempts')->default(0);
            $table->tinyInteger('login_max_attempts')->default(10);
            $table->string('app_language')->default('en');
            $table->text('address')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->index([
                'phone',
                'email',
                'gender',
            ]);
            $table->unique(['phone', 'email']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid(' user_id')->index();
            $table->string('token');
            $table->string('channel', 20)->comment('sms,email');
            $table->tinyInteger('attempts')->default(0);
            $table->tinyInteger('max_attempts')->default(5);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignUuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
