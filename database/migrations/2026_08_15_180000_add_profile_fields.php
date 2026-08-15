<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::table('users', function (Blueprint $table) {
   $table->string('headline')->nullable()->after('avatar');
   $table->text('bio')->nullable()->after('headline');
  });
  Schema::table('learning_path_items', function (Blueprint $table) {
   $table->text('child_voice')->nullable()->after('reason');
  });
 }
 public function down(): void {
  Schema::table('users', function (Blueprint $table) { $table->dropColumn(['headline','bio']); });
  Schema::table('learning_path_items', function (Blueprint $table) { $table->dropColumn('child_voice'); });
 }
};
