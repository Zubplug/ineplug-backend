<?php

// database/migrations/xxxx_xx_xx_add_last_logout_at_to_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_logout_at')->nullable()->after('email_verified_at');
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_logout_at');
        });
    }
};

