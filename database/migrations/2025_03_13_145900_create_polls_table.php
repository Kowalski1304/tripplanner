<?php

use App\Models\Chat;
use App\Models\User;
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
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->foreignIdFor(Chat::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'creator_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->dropForeign(['chat_id']);
            $table->dropForeign(['creator_id']);
        });

        Schema::dropIfExists('polls');
    }
};
