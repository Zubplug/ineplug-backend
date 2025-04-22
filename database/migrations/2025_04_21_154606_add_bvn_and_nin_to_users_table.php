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
    Schema::table('users', function ($table) {
        $table->string('bvn')->nullable()->after('dob');
        $table->string('nin')->nullable()->after('bvn');
    });
}

public function down()
{
    Schema::table('users', function ($table) {
        $table->dropColumn(['bvn', 'nin']);
    });
}

};
