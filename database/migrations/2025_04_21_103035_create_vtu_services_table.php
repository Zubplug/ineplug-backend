<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vtu_services', function (Blueprint $table) {
            $table->id();
            $table->string('service_id');
            $table->string('variation_code')->nullable();
            $table->string('name');
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('agent_commission', 10, 2)->default(0);
            $table->decimal('aggregator_commission', 10, 2)->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['service_id', 'variation_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vtu_services');
    }
};
