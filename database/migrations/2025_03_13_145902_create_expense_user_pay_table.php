<?php

use App\Models\Expense;
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
        Schema::create('expense_user_pay', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('amount', 10, 2);
            $table->dateTime('paid_at')->nullable();
            $table->foreignIdFor(Expense::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_user_pay');
    }
};
