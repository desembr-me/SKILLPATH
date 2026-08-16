<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->unsignedSmallInteger('package_duration')->default(3)->after('schedule_id');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->unsignedSmallInteger('package_duration')->default(3)->after('schedule_id');
            $table->unsignedSmallInteger('total_sessions')->default(12)->after('package_duration');
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn('package_duration');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn(['package_duration', 'total_sessions']);
        });
    }
};
