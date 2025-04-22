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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->nullable();
            $table->string('subcategory')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('commission', 10, 2)->nullable();
            $table->string('access_right')->default('both'); // partner / company / both
            $table->string('service_type')->default('manual'); // manual / api
            $table->foreignId('parent_id')->nullable()->constrained('services')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
