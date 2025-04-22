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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // vtu, service, transfer, etc.
            $table->string('category')->nullable(); // airtime, data, electricity, etc.
            $table->string('reference')->unique(); // request ID or transaction ref
            $table->string('status')->default('pending'); // pending, successful, failed
            $table->decimal('amount', 12, 2);
            $table->string('recipient')->nullable(); // phone, account, meter, etc.
            $table->json('metadata')->nullable(); // VTpass API response or form data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
