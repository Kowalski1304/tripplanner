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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Chat::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'sender_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'recipient_id')->constrained()->cascadeOnDelete();
            $table->text('text');
            $table->json('read_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['chat_id']);
            $table->dropForeign(['sender_id']);
            $table->dropForeign(['recipient_id']);
        });

        Schema::dropIfExists('messages');
    }
};
