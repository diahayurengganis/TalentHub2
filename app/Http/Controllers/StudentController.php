<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\JobExperience;
use App\Models\Academic;
use App\Models\Achievement;
use App\Models\Certification;
use App\Models\RelatedProduct; // Sesuaikan jika nama model Anda related_product adalah Product/RelatedProduct
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentController extends Controller
{
    /**
     * Display a listing of students with reactive search and filters.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $prodi  = $request->input('prodi');

        // Menggunakan Eloquent dengan Eager Loading untuk efisiensi kueri relasi
        $students = Student::with('profile')
            ->select('id_mhs', 'name', 'prodi')
            ->when($search, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                             ->orWhere('id_mhs', 'like', "%{$search}%");
                });
            })
            ->when($prodi, function ($query, $prodi) {
                $query->where('prodi', $prodi);
            })
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Landing/SearchStudent', [
            'students' => $students,
            'filters'  => $request->only(['search', 'prodi'])
        ]);
    }

    /**
     * Display the detailed portfolio/digital CV of a specific student.
     */
    public function show($id_mhs)
    {
        // Mencari data student menggunakan model Eloquent
        $student = Student::with('profile')->where('id_mhs', $id_mhs)->first();

        if (!$student) {
            abort(404, 'Student profile record not found.');
        }

        // Memuat seluruh asset data portofolio menggunakan Model pendukung masing-masing
        $academics    = Certification::where('id_mhs', $id_mhs)->get();
        $achievements = Achievement::where('id_mhs', $id_mhs)->get();

        // 🌟 PERBAIKAN: Diurutkan berdasarkan tanggal mulai kerja terbaru (descending)
        $experiences  = JobExperience::where('id_mhs', $id_mhs)
            ->orderBy('start_date', 'desc')
            ->get();

        // Ganti 'Product' menjadi 'RelatedProduct' jika itu nama Class Model Anda
        $products     = RelatedProduct::where('id_mhs', $id_mhs)->get();

        // Memuat data kompetensi melalui model Student dengan join tabel master
        $competencies = $student->competencies()
            ->with('master')
            ->get()
            ->map(function($comp) {
                return [
                    'nama_matkul' => $comp->master?->nama_matkul,
                    'competency'  => $comp->master?->competency,
                    'nilai'       => $comp->nilai
                ];
            });

        return Inertia::render('Landing/StudentCV', [
            'student'      => $student,
            'profile'      => $student->profile,
            'academics'    => $academics,
            'achievements' => $achievements,
            'experiences'  => $experiences,
            'products'     => $products,
            'competencies' => $competencies
        ]);
    }
}
