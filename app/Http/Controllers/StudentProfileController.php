<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // 🌟 DITAMBAHKAN untuk manipulasi file storage
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class StudentProfileController extends Controller
{
    /**
     * Remove the current custom profile picture and fallback to default.jpg
     */
    public function destroyProfilePicture()
    {
        $nik = session('user_nik');
        if (!$nik) return back();

        try {
            $profile = StudentProfile::where('id_mhs', $nik)->first();

            if ($profile && $profile->profile_picture && $profile->profile_picture !== 'default.jpg' && $profile->profile_picture !== '-') {
                // 1. Hapus file fisik gambar dari disk 'public'
                if (Storage::disk('public')->exists('profile_picture/' . $profile->profile_picture)) {
                    Storage::disk('public')->delete('profile_picture/' . $profile->profile_picture);
                    Log::info("Custom profile picture safely unlinked from storage: " . $profile->profile_picture);
                }

                // 2. Kembalikan nama file di database ke 'default.jpg'
                $profile->update([
                    'profile_picture' => 'default.jpg'
                ]);

                return back()->with('flash_message', [
                    'display' => 'toast',
                    'type' => 'success',
                    'message' => 'Profile picture successfully restored to default.'
                ]);
            }

            return back();
        } catch (\Exception $e) {
            Log::error("Destroy Profile Picture Failed: " . $e->getMessage());
            return back()->with('flash_message', [
                'display' => 'modal',
                'type' => 'error',
                'title' => 'Removal Failure',
                'message' => 'Failed to discard custom avatar asset.'
            ]);
        }
    }

    /**
     * Process Biography, Contact, and Profile Picture Changes
     */
    public function updateProfile(Request $request)
    {
        Log::info("=== UPDATE PROFILE START ===");
        Log::info("Payload Request Details:", $request->except(['profile_picture'])); // Menghindari log binary data gambar penuh

        $nik = session('user_nik');
        Log::info("Session user_nik tracking code: " . ($nik ?? 'NULL / EMPTY'));

        if (!$nik) {
            Log::warning("Update canceled: Target Session NIK missing.");
            return back()->with('flash_message', [
                'display' => 'modal',
                'type'    => 'error',
                'title'   => 'Session Expired',
                'message' => 'Your session context has terminated. Please re-authenticate your credentials.'
            ]);
        }

        try {
            Log::info("Compiling authorization validation scripts...");

            // 🌟 PERBAIKAN 1: Tambahkan rule strict untuk profile_picture berkas gambar max 2MB
            $validator = Validator::make($request->all(), [
                'profile'         => 'nullable|string|max:1000',
                'personal_email'  => 'nullable|email|max:200',
                'phone'           => 'nullable|string|max:50',
                'address'         => 'nullable|string|max:255',
                'skill'           => 'nullable|string|max:255',
                'linkedin'        => 'nullable|url|max:200',
                'instagram'       => 'nullable|url|max:200',
                'twitter'         => 'nullable|url|max:200',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ], [
                'personal_email.email' => 'The email address format provided is invalid.',
                'linkedin.url'         => 'LinkedIn anchors must start with a valid https:// structure.',
                'instagram.url'        => 'Instagram links must start with a valid https:// structure.',
                'twitter.url'          => 'Twitter (X) connections must start with a valid https:// structure.',
                'profile_picture.image' => 'The uploaded file must be an image.',
                'profile_picture.mimes' => 'Profile picture must be a file of type: jpeg, png, jpg.',
                'profile_picture.max'   => 'Profile picture size may not be greater than 2MB.',
            ]);

            if ($validator->fails()) {
                Log::warning("Form request validation faulted! Active logs:", $validator->errors()->toArray());
                throw new ValidationException($validator);
            }

            Log::info("Validation passed successfully. Fetching active record for image synchronization tracking...");

            // Cari data profil mahasiswa saat ini untuk pengecekan file gambar lama
            $currentProfile = StudentProfile::where('id_mhs', $nik)->first();
            $fileNameToStore = $currentProfile?->profile_picture ?? 'default.jpg';

            // 🌟 PERBAIKAN 2: Logika Pemrosesan Upload File Foto Profil Baru
            if ($request->hasFile('profile_picture')) {
                Log::info("New raw profile picture detected inside request boundary payload. Initializing transfer stream...");

                $file = $request->file('profile_picture');
                // Format nama file baru: idmhs_timestamp.extension (contoh: 3311801013_1719875612.jpg)
                $fileNameToStore = $nik . '_' . time() . '.' . $file->getClientOriginalExtension();

                // Simpan berkas fisik gambar baru ke folder storage/app/public/profile_picture/
                // $file->storeAs('public/profile_picture', $fileNameToStore);
                $file->storeAs('profile_picture', $fileNameToStore, 'public');
                Log::info("New image binary written securely into file system name: " . $fileNameToStore);

                // Hapus foto lama dari disk server jika ada dan bukan file default default.jpg / -
                if ($currentProfile && $currentProfile->profile_picture && $currentProfile->profile_picture !== 'default.jpg' && $currentProfile->profile_picture !== '-') {
                    // 🌟 PERBAIKAN: Gunakan Storage::disk('public')->exists() dan delete()
                    if (Storage::disk('public')->exists('profile_picture/' . $currentProfile->profile_picture)) {
                        Storage::disk('public')->delete('profile_picture/' . $currentProfile->profile_picture);

                        Log::info("Obsolete outdated image file deleted cleanly from server storage path: " . $currentProfile->profile_picture);
                    }
                }
            }

            Log::info("Proceeding with updateOrCreate pipeline database query...");

            // 🌟 PERBAIKAN 3: Sinkronisasi database dengan menyimpan string nama file baru
            StudentProfile::updateOrCreate(
                ['id_mhs' => $nik],
                [
                    'profile'         => $request->profile ?? '-',
                    'personal_email'  => $request->personal_email,
                    'phone'           => $request->phone ?? '-',
                    'address'         => $request->address ?? '-',
                    'skill'           => $request->skill ?? '-',
                    'linkedin'        => $request->linkedin,
                    'instagram'       => $request->instagram,
                    'twitter'         => $request->twitter,
                    'profile_picture' => $fileNameToStore, // Menyimpan hanya string nama file baru
                    'updated_at'      => now(),
                ]
            );

            Log::info("Database successfully synchronized for ID: " . $nik);
            Log::info("=== UPDATE PROFILE END SUCCESS ===");

            return back()->with('flash_message', [
                'display' => 'toast',
                'type'    => 'success',
                'message' => 'Your background profile information has been successfully updated!'
            ]);

        } catch (ValidationException $e) {
            Log::error("ValidationException intercepted, bouncing error bag responses straight to Inertia fields.");
            throw $e;

        } catch (\Exception $e) {
            Log::error("FATAL INTERNAL EXCEPTION within updateProfile processing module:", [
                'id_mhs'     => $nik,
                'error_msg'  => $e->getMessage(),
                'line_error' => $e->getLine(),
                'file_error' => $e->getFile()
            ]);

            return back()->with('flash_message', [
                'display' => 'modal',
                'type'    => 'error',
                'title'   => 'Save Failure',
                'message' => 'An internal database save interception error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function show($id_mhs)
    {
        // 🌟 PERBAIKAN: Mengganti academics menjadi certifications sesuai skema tabel baru
        $student = Student::with('profile')->find($id_mhs);

        if (!$student) {
            abort(404, 'Student profile record not found.');
        }

        $sessionKey = 'viewed_student_' . $id_mhs;
        if (!session()->has($sessionKey)) {

            if ($student->profile) {
                $student->profile()->increment('views_count');
            } else {
                StudentProfile::create([
                    'id_mhs'          => $id_mhs,
                    'profile'         => '-',
                    'gpa'             => '0.00',
                    'profile_picture' => 'default.jpg',
                    'address'         => '-',
                    'phone'           => '-',
                    'skill'           => '-',
                    'views_count'     => 1,
                ]);
            }

            session()->put($sessionKey, true);
            $student->load('profile');
        }

        return Inertia::render('Landing/StudentCV', [
            'student'      => $student,
            'profile'      => $student->profile,
            // 🌟 PERBAIKAN: Mengambil data relasi dari model certifications terbaru
            'certifications' => $student->certifications()->orderBy('start_date', 'desc')->get(),
            'achievements' => $student->achievements,
            'experiences'  => $student->experiences()->orderBy('start_date', 'desc')->get(),
            'products'     => $student->products,
            'competencies' => $student->competencies()->with('master')->get()->map(function($comp) {
                return [
                    'nama_matkul' => $comp->master?->nama_matkul,
                    'competency'  => $comp->master?->competency,
                    'nilai'       => $comp->nilai
                ];
            })
        ]);
    }

    /**
     * Main Portfolio Workspace Console
     */
    public function index()
    {
        $nik = session('user_nik');

        if (!$nik) {
            return redirect()->route('login.student')->with('flash_message', [
                'display' => 'modal',
                'type'    => 'error',
                'title'   => 'Session Expired',
                'message' => 'Your authentication session has expired. Please sign in again.'
            ]);
        }

        // 🌟 PERBAIKAN: Memuat eager loading model 'certifications' (menggantikan 'academics')
        $student = Student::with([
            'profile',
            'certifications',
            'achievements',
            // 🌟 PERBAIKAN: Urutkan data pendidikan dari tahun kelulusan paling baru ke lama
            'educations' => function($query) {
                $query->orderBy('year_end', 'desc');
            },
            'experiences',
            'products',
            'languages.attachments',
            'competencies.master'
        ])->find($nik);

        if ($student && !$student->profile) {
            $student->profile()->create([
                'profile' => '-', 'gpa' => '0.00', 'personal_email' => session('email', '-'),
                'profile_picture' => 'default.jpg', 'address' => '-', 'phone' => '-', 'skill' => '-'
            ]);
            $student->load('profile');
        }

        return Inertia::render('Student/Dashboard', [
            'student'        => $student,
            'profile'        => $student->profile,
            // 🌟 PERBAIKAN: Mengirimkan array objek dari certifications terbaru
            'certifications' => $student->certifications,
            'achievements'   => $student->achievements,
            'educations'     => $student->educations,
            'experiences'    => $student->experiences,
            'products'       => $student->products,
            'languages'      => $student->languages,
            'competencies'   => $student->competencies
        ]);
    }

}
