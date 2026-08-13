<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('final_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_path_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('title', 160)->default('Ujian Akhir');
            $table->unsignedTinyInteger('passing_score')->default(75);
            $table->unsignedTinyInteger('max_attempts')->default(3);
            $table->json('questions');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('final_exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('child_profile_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('attempt_number');
            $table->decimal('score', 5, 2);
            $table->boolean('passed')->default(false)->index();
            $table->json('answers')->nullable();
            $table->timestamp('completed_at');
            $table->timestamps();
            $table->unique(
                ['final_exam_id', 'child_profile_id', 'attempt_number'],
                'exam_attempt_unique'
            );
        });


        DB::table('learning_paths')
            ->where('certificate_enabled', true)
            ->orderBy('id')
            ->get(['id', 'title', 'learning_outcomes'])
            ->each(function ($course) {
                $outcome = $course->learning_outcomes ?: 'menerapkan keterampilan utama yang dipelajari pada course';
                $questions = [
                    [
                        'question' => 'Apa hasil belajar utama yang diharapkan dari course '.$course->title.'?',
                        'options' => [$outcome, 'Menghafal semua materi tanpa praktik', 'Menyelesaikan course secepat mungkin', 'Menghindari latihan yang menantang'],
                        'correct' => 0,
                    ],
                    [
                        'question' => 'Saat menemui bagian yang sulit, langkah belajar yang paling tepat adalah...',
                        'options' => ['Mencoba kembali secara bertahap dan meminta bantuan bila diperlukan', 'Langsung melewati semua latihan', 'Menyalin jawaban tanpa memahami', 'Berhenti belajar selamanya'],
                        'correct' => 0,
                    ],
                    [
                        'question' => 'Mengapa latihan dan project penting dalam course ini?',
                        'options' => ['Agar keterampilan dapat diterapkan, bukan hanya diketahui', 'Agar halaman course terlihat lebih panjang', 'Agar tidak perlu memahami konsep', 'Agar nilai selalu otomatis sempurna'],
                        'correct' => 0,
                    ],
                    [
                        'question' => 'Apa cara yang baik untuk menilai perkembangan diri setelah belajar?',
                        'options' => ['Membandingkan kemampuan sekarang dengan kemampuan sebelum berlatih', 'Hanya membandingkan diri dengan teman', 'Mengabaikan semua umpan balik', 'Mengulang tanpa refleksi'],
                        'correct' => 0,
                    ],
                    [
                        'question' => 'Jika sebuah aktivitas belum berhasil pada percobaan pertama, sikap yang paling tepat adalah...',
                        'options' => ['Mengevaluasi kesalahan, mencoba strategi lain, lalu berlatih lagi', 'Menganggap diri tidak mampu', 'Menghapus semua progres', 'Memilih jawaban secara acak terus-menerus'],
                        'correct' => 0,
                    ],
                ];

                DB::table('final_exams')->insert([
                    'learning_path_id' => $course->id,
                    'title' => 'Ujian Akhir - '.$course->title,
                    'passing_score' => 75,
                    'max_attempts' => 3,
                    'questions' => json_encode($questions, JSON_UNESCAPED_UNICODE),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
        Schema::dropIfExists('final_exams');
    }
};
