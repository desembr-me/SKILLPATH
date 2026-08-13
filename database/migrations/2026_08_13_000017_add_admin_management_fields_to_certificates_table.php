<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->string('status', 20)->default('active')->index();
            $table->foreignId('issued_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('revoked_at')->nullable();
            $table->text('revoked_reason')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropForeign(['issued_by']);
            $table->dropColumn([
                'status',
                'issued_by',
                'revoked_at',
                'revoked_reason',
            ]);
        });
    }
};
