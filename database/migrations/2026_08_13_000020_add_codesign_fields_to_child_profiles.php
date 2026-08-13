<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('child_profiles', function (Blueprint $table) {
            $table->foreignId('favorite_interest_id')->nullable()->after('avatar')->constrained('interests')->nullOnDelete();
            $table->string('learning_need', 40)->nullable()->after('favorite_interest_id');
            $table->text('child_voice')->nullable()->after('learning_need');
            $table->timestamp('co_design_completed_at')->nullable()->after('child_voice');
        });
    }

    public function down(): void
    {
        Schema::table('child_profiles', function (Blueprint $table) {
            $table->dropForeign(['favorite_interest_id']);
            $table->dropColumn(['favorite_interest_id', 'learning_need', 'child_voice', 'co_design_completed_at']);
        });
    }
};
