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
        Schema::table('users', function ($table) {
            $table->boolean('pnd')->default(false); // Post No Debit
            $table->boolean('address_verified')->default(false); // For manual admin approval
        });
    }
    
    public function down(): void
    {
        Schema::table('users', function ($table) {
            $table->dropColumn(['pnd', 'address_verified']);
        });
    }
    
};
