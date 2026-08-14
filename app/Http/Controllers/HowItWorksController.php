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
                'title' => 'Pilih kelas yang sesuai',
                'description' => 'Cari kelas berdasarkan usia, minat, kategori, pengajar, lokasi, dan jadwal.',
            ],
            [
                'number' => 3,
                'title' => 'Pesan kursi kelas',
                'description' => 'Pilih sesi tatap muka yang tersedia dan selesaikan pendaftaran untuk anak.',
            ],
            [
                'number' => 4,
                'title' => 'Datang dan belajar',
                'description' => 'Anak hadir di lokasi sesuai jadwal untuk praktik dan belajar langsung bersama pengajar.',
            ],
            [
                'number' => 5,
                'title' => 'Pantau perkembangan',
                'description' => 'Orang tua dapat melihat aktivitas, progres, dan riwayat kelas melalui dashboard.',
            ],
        ];

        return view('pages.how-it-works', compact('steps'));
    }
}
