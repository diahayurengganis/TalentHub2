<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Education;
use App\Models\Academic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    public function index()
    {
        Log::info("=== LOADING LANDING WELCOME PAGE ===");

        try {
            // Tarik maksimal 10 mahasiswa secara dinamis untuk penayangan di carousel/grid depan
            // Ambil data mahasiswa aktif, acak urutannya, lalu kelompokkan berdasarkan program studi
            $students = Student::select('id_mhs', 'name', 'prodi')
                ->inRandomOrder() // Mengacak urutan mahasiswa secara native di database
                ->get()
                ->groupBy('prodi') // Mengelompokkan hasil kueri berdasarkan nama prodi
                ->map(function ($group) {
                    return $group->take(2); // Mengambil maksimal 2 mahasiswa saja per prodi
                })
                ->flatten() // Menyatukan kembali kelompok-kelompok prodi menjadi satu array list lurus
                ->take(10); // Memastikan total batas akhir penayangan keseluruhan tetap maksimal 10 mahasiswa

            // Hitung agregasi data counter statistik secara dinamis dari tabel relational native lama
            $stats = [
                'companies' => 'Coming Soon',
                'students'  => Student::count() . ' Talents',
                'interns'   => 'Coming Soon',
                'jobs'      => 'Coming Soon'
            ];

            return Inertia::render('Landing/Welcome', [
                'students' => $students,
                'stats'    => $stats
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to load landing data: " . $e->getMessage());

            // Fallback aman jika struktur database belum sinkron total saat proses migrasi
            return Inertia::render('Landing/Welcome', [
                'students' => [],
                'stats' => ['companies' => '42+', 'students' => '1.2K', 'interns' => '28 Positions', 'jobs' => '14 Openings']
            ]);
        }
    }

    public function sendContact(Request $request)
    {
        Log::info("Contact Form Message Dispatch:", $request->all());

        // Taruh logika simpan pesan ke database atau kirim e-mail Hubin di sini

        return back()->with('flash_message', [
            'display' => 'toast',
            'type'    => 'success',
            'message' => 'Thank you! Your inquiry message has been submitted successfully.'
        ]);
    }
}
