<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('class_sessions')) {
            Schema::create('class_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('learning_path_id')->constrained()->cascadeOnDelete();
                $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
                $table->string('title', 150);
                $table->text('description')->nullable();
                $table->dateTime('starts_at');
                $table->dateTime('ends_at');
                $table->string('venue_name', 180);
                $table->text('address');
                $table->string('room', 100)->nullable();
                $table->string('map_url')->nullable();
                $table->unsignedInteger('capacity')->default(20);
                $table->string('status', 20)->default('scheduled');
                $table->text('preparation_notes')->nullable();
                $table->timestamps();
            });
        }

        // Existing installations may still contain the old live-learning tables.
        // Preserve their schedule records as offline sessions before removing them.
        if (Schema::hasTable('live_sessions')) {
            DB::table('live_sessions')->orderBy('id')->get()->each(function ($legacy) {
                if (DB::table('class_sessions')->where('id', $legacy->id)->exists()) {
                    return;
                }

                $status = in_array($legacy->status, ['completed', 'cancelled'], true)
                    ? $legacy->status
                    : 'scheduled';

                DB::table('class_sessions')->insert([
                    'id' => $legacy->id,
                    'learning_path_id' => $legacy->learning_path_id,
                    'instructor_id' => $legacy->instructor_id,
                    'title' => $legacy->title,
                    'description' => $legacy->description,
                    'starts_at' => $legacy->starts_at,
                    'ends_at' => $legacy->ends_at,
                    'venue_name' => 'Lokasi perlu diperbarui',
                    'address' => 'Alamat belum tersedia. Perbarui jadwal ini dari dashboard admin atau pengajar.',
                    'room' => null,
                    'map_url' => null,
                    'capacity' => $legacy->capacity ?: 20,
                    'status' => $status,
                    'preparation_notes' => 'Jadwal ini dimigrasikan dari versi kelas online. Lengkapi lokasi dan persiapan sebelum dipublikasikan kepada peserta.',
                    'created_at' => $legacy->created_at,
                    'updated_at' => $legacy->updated_at,
                ]);
            });
        }

        if (Schema::hasTable('session_bookings') && Schema::hasColumn('session_bookings', 'live_session_id')) {
            Schema::create('session_bookings_offline_tmp', function (Blueprint $table) {
                $table->id();
                $table->foreignId('class_session_id')->constrained('class_sessions')->cascadeOnDelete();
                $table->foreignId('child_profile_id')->constrained()->cascadeOnDelete();
                $table->string('status', 20)->default('booked');
                $table->timestamp('booked_at')->nullable();
                $table->timestamp('checked_in_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->unique(['class_session_id', 'child_profile_id'], 'offline_session_child_unique');
            });

            DB::table('session_bookings')->orderBy('id')->get()->each(function ($legacy) {
                if (! DB::table('class_sessions')->where('id', $legacy->live_session_id)->exists()) {
                    return;
                }

                DB::table('session_bookings_offline_tmp')->insert([
                    'id' => $legacy->id,
                    'class_session_id' => $legacy->live_session_id,
                    'child_profile_id' => $legacy->child_profile_id,
                    'status' => in_array($legacy->status, ['booked', 'cancelled'], true) ? $legacy->status : 'booked',
                    'booked_at' => $legacy->booked_at,
                    'checked_in_at' => null,
                    'notes' => 'Booking dimigrasikan dari jadwal kelas online.',
                    'created_at' => $legacy->created_at,
                    'updated_at' => $legacy->updated_at,
                ]);
            });

            Schema::drop('session_bookings');
            Schema::rename('session_bookings_offline_tmp', 'session_bookings');
        } elseif (! Schema::hasTable('session_bookings')) {
            Schema::create('session_bookings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('class_session_id')->constrained()->cascadeOnDelete();
                $table->foreignId('child_profile_id')->constrained()->cascadeOnDelete();
                $table->string('status', 20)->default('booked');
                $table->timestamp('booked_at')->nullable();
                $table->timestamp('checked_in_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->unique(['class_session_id', 'child_profile_id']);
            });
        }

        if (Schema::hasTable('live_sessions')) {
            Schema::drop('live_sessions');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('session_bookings');
        Schema::dropIfExists('class_sessions');
    }
};
