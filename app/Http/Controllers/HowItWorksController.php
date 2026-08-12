<?php

namespace App\Http\Controllers;

class HowItWorksController extends Controller
{
    public function __invoke()
    {
        $steps = [
            [
                'number' => 1,
                'title' => 'Buat profil anak',
                'description' => 'Orang tua mengisi nama panggilan, usia 5–14 tahun, dan minat anak.',
            ],
            [
                'number' => 2,
                'title' => 'Sistem menyaring jalur',
                'description' => 'SKILLPATH memfilter jalur berdasarkan rentang usia dan menghitung kecocokan minat.',
            ],
            [
                'number' => 3,
                'title' => 'Anak mulai aktivitas',
                'description' => 'Anak belajar melalui modul dan aktivitas singkat yang terstruktur.',
            ],
            [
                'number' => 4,
                'title' => 'Progres disimpan',
                'description' => 'Aktivitas yang selesai, poin, dan progres jalur tersimpan di database.',
            ],
            [
                'number' => 5,
                'title' => 'Rekomendasi diperbarui',
                'description' => 'Jalur yang sudah dimulai mendapat prioritas agar pengalaman belajar tetap berkelanjutan.',
            ],
        ];

        return view('pages.how-it-works', compact('steps'));
    }
}
