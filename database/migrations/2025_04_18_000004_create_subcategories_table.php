<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('subcategories', function (Blueprint $table) {
            if (!Schema::hasColumn('subcategories', 'commission_partner')) {
                $table->integer('commission_partner')->nullable();
            }
            if (!Schema::hasColumn('subcategories', 'commission_agent')) {
                $table->integer('commission_agent')->nullable();
            }
            if (!Schema::hasColumn('subcategories', 'commission_aggregator')) {
                $table->integer('commission_aggregator')->nullable();
            }
        });
    }
    

    public function down(): void
    {
        Schema::dropIfExists('subcategories');
    }
};
