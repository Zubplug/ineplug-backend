<?php

// database/migrations/xxxx_xx_xx_create_service_requests_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('user_name');
            $table->string('service_type'); // e.g., BVN
            $table->unsignedBigInteger('main_service_id')->nullable();
            $table->unsignedBigInteger('subcategory_id')->nullable();
            $table->unsignedBigInteger('sub_subcategory_id')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->text('submitted_data')->nullable(); // optional form content
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('service_requests');
    }
};

