<?php

use App\Models\Poll;
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
        Schema::create('poll_options', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Poll::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->nullOnDelete();
            $table->string('option_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('poll_options', function (Blueprint $table) {
            $table->dropForeign(['poll_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('poll_options');
    }
};
