<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Inertia\Inertia;

class TUController extends Controller
{
    public function index()
    {
        return Inertia::render('TU/Dashboard');
    }

    public function verificationIndex(Request $request)
    {
        // 1. Ambil data dummy awal
        $defaultStudents = [
            [
                'id' => 1,
                'name' => 'Afdal',
                'nim' => '3312401001',
                'status_verifikasi' => 'Pending'
            ],
            [
                'id' => 2,
                'name' => 'Siti Rahma',
                'nim' => '3312401002',
                'status_verifikasi' => 'Approved'
            ],
            [
                'id' => 3,
                'name' => 'Ahmad Fauzi',
                'nim' => '3312401003',
                'status_verifikasi' => 'Rejected'
            ]
        ];

        // 2. Ambil perubahan status dari Session jika ada (supaya status tetap tersimpan saat demo)
        $sessionStatus = $request->session()->get('demo_student_statuses', []);

        // 3. Gabungkan status terbaru
        $students = array_map(function ($student) use ($sessionStatus) {
            if (isset($sessionStatus[$student['id']])) {
                $student['status_verifikasi'] = $sessionStatus[$student['id']];
            }
            return $student;
        }, $defaultStudents);

        return Inertia::render('TU/VerificationIndex', [
            'students' => $students
        ]);
    }

    public function updateVerification(Request $request, $id)
    {
        $request->validate([
            'status_verifikasi' => 'required|in:Approved,Rejected,Pending',
        ]);

        // Simpan status baru ke dalam Session sementara
        $sessionStatus = $request->session()->get('demo_student_statuses', []);
        $sessionStatus[$id] = $request->input('status_verifikasi');
        $request->session()->put('demo_student_statuses', $sessionStatus);

        // Update ke database jika tabel database sudah ada
        try {
            $student = Student::find($id);
            if ($student) {
                $student->update([
                    'status_verifikasi' => $request->input('status_verifikasi')
                ]);
            }
        } catch (\Exception $e) {
            // Abaikan jika database belum siap
        }

        return redirect()->back()->with('success', 'Status verifikasi berhasil diperbarui.');
    }
}