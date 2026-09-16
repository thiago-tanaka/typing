<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

// Laravel 10+ stores password reset tokens in `password_reset_tokens`. The columns
// (email, token, created_at) are the same, so the old table only needs a new name.
return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('password_resets', 'password_reset_tokens');
    }

    public function down(): void
    {
        Schema::rename('password_reset_tokens', 'password_resets');
    }
};
