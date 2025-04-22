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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Personal details
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('name'); // full name (e.g., John Doe)

            // Contact
            $table->string('email')->unique();
            $table->string('phone')->unique();

            // Optional (defaulted for partner staff registration)
            $table->string('gender')->nullable();
            $table->date('dob')->nullable();
            $table->text('address')->nullable();

            // Referral (used for Aggregator or others)
            $table->string('referral_code')->nullable();

            // Security
            $table->string('transaction_pin')->nullable(); // Nullable to allow registration without it
            $table->string('password');

            // System controls
            $table->decimal('wallet_balance', 10, 2)->default(0);
            $table->string('role')->default('User'); // Can be 'User', 'Admin', 'Partner Staff', etc.
            $table->string('status')->default('Pending'); // New: Active, Suspended, Pending
            $table->integer('kyc_level')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
