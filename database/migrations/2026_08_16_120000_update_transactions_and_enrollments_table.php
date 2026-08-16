<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add transaction_id to enrollments table if not exists
        if (!Schema::hasColumn('enrollments', 'transaction_id')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->foreignId('transaction_id')
                    ->nullable()
                    ->after('parent_id')
                    ->constrained('transactions')
                    ->nullOnDelete();
            });
        }

        // 2. Make enrollment_id nullable in transactions table
        if (Schema::hasColumn('transactions', 'enrollment_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->unsignedBigInteger('enrollment_id')->nullable()->change();
            });
        }

        // 3. Backfill data: link existing enrollments with their transactions
        $transactions = DB::table('transactions')->whereNotNull('enrollment_id')->get();
        foreach ($transactions as $tx) {
            DB::table('enrollments')
                ->where('id', $tx->enrollment_id)
                ->whereNull('transaction_id')
                ->update(['transaction_id' => $tx->id]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('enrollments', 'transaction_id')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->dropForeign(['transaction_id']);
                $table->dropColumn('transaction_id');
            });
        }

        if (Schema::hasColumn('transactions', 'enrollment_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->unsignedBigInteger('enrollment_id')->nullable(false)->change();
            });
        }
    }
};
