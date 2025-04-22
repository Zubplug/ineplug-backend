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
        Schema::table('users', function (Blueprint $table) {
            $table->string('gender')->nullable()->change();
            $table->date('dob')->nullable()->change();
            $table->text('address')->nullable()->change();
            $table->string('transaction_pin')->nullable()->change();

            $table->string('status')->default('Pending')->after('role'); // Add status column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('gender')->change();
            $table->date('dob')->change();
            $table->text('address')->change();
            $table->string('transaction_pin')->change();

            $table->dropColumn('status');
        });
    }
};
