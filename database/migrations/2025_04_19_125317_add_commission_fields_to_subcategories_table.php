<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subcategories', function (Blueprint $table) {
            $table->integer('commission_partner')->nullable();
            $table->integer('commission_agent')->nullable();
            $table->integer('commission_aggregator')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('subcategories', function (Blueprint $table) {
            $table->dropColumn(['commission_partner', 'commission_agent', 'commission_aggregator']);
        });
    }
};
