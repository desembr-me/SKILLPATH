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
                'title' => 'Temukan kelas yang cocok',
                'description' => 'SKILLPATH menyaring kelas non-akademik berdasarkan usia, minat, jadwal, dan ketersediaan program.',
            ],
            [
                'number' => 3,
                'title' => 'Daftar atau beli kelas',
                'description' => 'Orang tua mendaftarkan anak ke kelas gratis atau menyelesaikan pembayaran untuk kelas berbayar.',
            ],
            [
                'number' => 4,
                'title' => 'Pilih jadwal tatap muka',
                'description' => 'Setelah terdaftar, pilih sesi yang tersedia dan cek lokasi, ruangan, waktu, serta perlengkapan yang perlu dibawa.',
            ],
            [
                'number' => 5,
                'title' => 'Hadir dan selesaikan program',
                'description' => 'Pengajar mencatat kehadiran setiap sesi. Riwayat kehadiran menjadi dasar penyelesaian kelas dan sertifikat bila tersedia.',
            ],
        ];

        return view('pages.how-it-works', compact('steps'));
    }
}
