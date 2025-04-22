<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('sub_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subcategory_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['manual', 'api'])->default('manual');
            $table->enum('fee_type', ['naira', 'percent'])->default('naira');
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('commission_partner', 10, 2)->nullable();
            $table->decimal('commission_agent', 10, 2)->nullable();
            $table->decimal('commission_aggregator', 10, 2)->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }
    

    public function down(): void
    {
        Schema::dropIfExists('sub_subcategories');
    }
};
