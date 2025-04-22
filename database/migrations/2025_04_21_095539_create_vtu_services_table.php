<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('vtu_services', function (Blueprint $table) {
            $table->id();
            $table->string('service_id'); // e.g. mtn, dstv
            $table->string('variation_code'); // e.g. mtn100
            $table->string('name');
            $table->decimal('price', 10, 2)->nullable(); // admin price
            $table->decimal('agent_commission', 10, 2)->default(0);
            $table->decimal('aggregator_commission', 10, 2)->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
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
