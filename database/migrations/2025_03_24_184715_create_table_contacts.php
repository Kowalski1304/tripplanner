<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('contact_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'contact_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
