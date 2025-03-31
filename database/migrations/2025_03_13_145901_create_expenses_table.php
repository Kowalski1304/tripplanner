<?php

use App\Models\Currency;
use App\Models\ExpenseCategory;
use App\Models\Team;
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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->nullOnDelete();
            $table->foreignIdFor(Team::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ExpenseCategory::class, 'category_id')->nullable()->constrained('expense_categories')->nullOnDelete();
            $table->foreignIdFor(Currency::class, 'currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->text('details')->nullable();

            // TODO expensed_add

            $table->date('expense_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['team_id']);
            $table->dropForeign(['category_id']);
            $table->dropForeign(['currency_id']);
        });

        Schema::dropIfExists('expenses');
    }
};
