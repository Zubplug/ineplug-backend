<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kyc_limits', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('kyc_level')->unique();
            $table->decimal('daily_limit', 14, 2)->default(0);
            $table->decimal('monthly_limit', 14, 2)->default(0);
            $table->decimal('lifetime_limit', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kyc_limits');
    }
};
