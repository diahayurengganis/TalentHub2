<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentVerification;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class TUController extends Controller
{
    // Menampilkan halaman login TU
    public function showLogin(Request $request)
    {
        if ($request->session()->get('tu_authenticated') === true) {
            return redirect()->route('tu.verifications.index');
        }

        return Inertia::render('TU/Login');
    }

    // Memproses login TU
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $demoUsername = (string) config('talenthub.tu_demo.username');
        $demoPassword = (string) config('talenthub.tu_demo.password');

        if (
            $demoUsername === '' ||
            $demoPassword === '' ||
            !hash_equals($demoUsername, $credentials['username']) ||
            !hash_equals($demoPassword, $credentials['password'])
        ) {
            return back()->withErrors([
                'username' => 'Username atau password TU salah.',
            ]);
        }

        $request->session()->regenerate();

        $request->session()->put([
    'tu_authenticated' => true,
    'tu_username' => $credentials['username'],
    'tu_last_activity' => time(),
]);

        return redirect()->route('tu.verifications.index')
            ->with('success', 'Login TU berhasil.');
    }

    // Logout TU
    public function logout(Request $request)
    {
        $request->session()->forget([
    'tu_authenticated',
    'tu_username',
    'tu_last_activity',
]);
        $request->session()->regenerateToken();

        return redirect()->route('tu.login')
            ->with('success', 'Kamu berhasil logout.');
    }

    // Memeriksa apakah pengguna sudah login sebagai TU
    private function ensureTUAuthenticated(Request $request)
    {
        if ($request->session()->get('tu_authenticated') !== true) {
            return redirect()->route('tu.login');
        }

        return null;
    }

    // Menampilkan halaman verifikasi
   



public function verificationIndex(Request $request)
{
    if ($response = $this->ensureTUAuthenticated($request)) {
        return $response;
    }

    $search = trim((string) $request->query('search', ''));
    $status = $request->query('status', 'All');

    if (!in_array($status, ['All', 'Pending', 'Approved', 'Rejected'], true)) {
        $status = 'All';
    }

    
$query = Student::query()
    ->leftJoin(
        'student_verifications',
        'student.id_mhs',
        '=',
        'student_verifications.id_mhs'
    )
    ->leftJoin(
        'student_profile',
        'student.id_mhs',
        '=',
        'student_profile.id_mhs'
    )
    ->select([
        'student.id_mhs',
        'student.name',
        'student.prodi',
        'student.jurusan',
        'student_verifications.status_verifikasi',
        'student_profile.profile as profile_bio',
        'student_profile.gpa',
        'student_profile.personal_email',
        'student_profile.profile_picture',
        'student_profile.address',
        'student_profile.phone',
        'student_profile.skill',
        'student_profile.linkedin',
        'student_profile.instagram',
        'student_profile.twitter',
        'student_profile.facebook',
    ]);


    if ($search !== '') {
        $query->where(function ($q) use ($search) {
            $q->where('student.name', 'like', "%{$search}%")
                ->orWhere('student.id_mhs', 'like', "%{$search}%");
        });
    }

    if ($status === 'Pending') {
        $query->where(function ($q) {
            $q->whereNull('student_verifications.id')
                ->orWhere('student_verifications.status_verifikasi', 'Pending');
        });
    } elseif (in_array($status, ['Approved', 'Rejected'], true)) {
        $query->where(
            'student_verifications.status_verifikasi',
            $status
        );
    }

    $students = $query
        ->orderBy('student.name')
        ->paginate(15)
        ->withQueryString();

    
$students->getCollection()->transform(function ($student) {
    return [
        'id' => $student->id_mhs,
        'name' => $student->name,
        'nim' => $student->id_mhs,
        'prodi' => $student->prodi,
        'jurusan' => $student->jurusan,
        'status_verifikasi' => $student->status_verifikasi ?: 'Pending',

        'profile' => $student->profile_bio,
        'gpa' => $student->gpa,
        'personal_email' => $student->personal_email,
        'profile_picture' => $student->profile_picture,
        'address' => $student->address,
        'phone' => $student->phone,
        'skill' => $student->skill,
        'linkedin' => $student->linkedin,
        'instagram' => $student->instagram,
        'twitter' => $student->twitter,
        'facebook' => $student->facebook,
    ];
});


    $totalStudents = Student::count();

    $approved = DB::table('student_verifications')
        ->where('status_verifikasi', 'Approved')
        ->distinct('id_mhs')
        ->count('id_mhs');

    $rejected = DB::table('student_verifications')
        ->where('status_verifikasi', 'Rejected')
        ->distinct('id_mhs')
        ->count('id_mhs');

    return Inertia::render('TU/VerificationIndex', [
        'students' => $students,
        'stats' => [
            'pending' => max(0, $totalStudents - $approved - $rejected),
            'approved' => $approved,
            'rejected' => $rejected,
            'total' => $totalStudents,
        ],
        'filters' => [
            'search' => $search,
            'status' => $status,
        ],
    ]);
}



    // Memperbarui status verifikasi
    
public function updateVerification(Request $request, $id)
{
    if ($response = $this->ensureTUAuthenticated($request)) {
        return $response;
    }

    $validated = $request->validate([
        'status_verifikasi' => 'required|in:Approved,Rejected,Pending',
    ]);

    $student = Student::where('id_mhs', $id)->firstOrFail();

    StudentVerification::updateOrCreate(
        ['id_mhs' => $student->id_mhs],
        [
            'status_verifikasi' => $validated['status_verifikasi'],
            'verified_by' => $request->session()->get('tu_username'),
        ]
    );

    return redirect()->back()->with(
        'success',
        'Status verifikasi berhasil disimpan ke database.'
    );
}

}
