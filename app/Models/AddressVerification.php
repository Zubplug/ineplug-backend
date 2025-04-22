<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddressVerification extends Model
{
    public function up()
{
    Schema::create('address_verifications', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->text('address');
        $table->string('document_path');
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        $table->timestamps();
    });
}

    //
}
