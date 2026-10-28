<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showStudentLogin()
    {
        return Inertia::render('Auth/Login');
    }

    public function attemptStudentLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        
        // Login pengujian lokal sesuai arahan Manpro
        if (app()->environment('local')) {
            $student = Student::find($request->username);

            if (!$student) {
                return back()->with('flash_message', [
                    'display' => 'modal',
                    'type' => 'error',
                    'title' => 'Student Not Found',
                    'message' => 'NIM tidak ditemukan di database lokal. Gunakan NIM mahasiswa yang tersedia di tabel student.'
                ]);
            }

            session([
                'user_nik' => $student->id_mhs,
                'user_jabatan' => 'Mahasiswa',
                'nama' => $student->name,
            ]);

            return redirect()->route('dashboard')->with(
                'flash_message',
                [
                    'display' => 'toast',
                    'type' => 'success',
                    'message' => 'Login pengujian lokal berhasil!'
                ]
            );
        }


        $apiUrl = 'https://sid.polibatam.ac.id/api/v1.php';

        try {
            // ==========================================
            // STEP 1: AUTHENTICATION TO OBTAIN SECRET KEY
            // ==========================================
            $loginResponse = Http::asForm()
                ->timeout(10)
                ->withoutVerifying()
                ->withHeaders(['User-Agent' => 'Student Login System'])
                ->post($apiUrl, [
                    'act'      => 'Login',
                    'username' => $request->username,
                    'password' => $request->password,
                ]);

            if ($loginResponse->failed()) {
                return back()->with('flash_message', [
                    'display' => 'modal',
                    'type'    => 'error',
                    'title'   => 'Connection Failed',
                    'message' => 'Unable to connect to the campus gateway service. Please try again later.'
                ]);
            }

            $loginResult = $loginResponse->json();

            if (!isset($loginResult['error_code']) || $loginResult['error_code'] != 0) {
                return back()->with('flash_message', [
                    'display' => 'modal',
                    'type'    => 'error',
                    'title'   => 'Sign In Failed',
                    'message' => $loginResult['error_desc'] ?? 'Your credentials are incorrect or not registered.'
                ]);
            }

            $secretKey = $loginResult['data']['secretkey'] ?? null;

            if (!$secretKey) {
                return back()->with('flash_message', [
                    'display' => 'modal',
                    'type'    => 'error',
                    'title'   => 'Authentication Error',
                    'message' => 'The authentication token key was not found. Please contact the administrator.'
                ]);
            }

            // ==========================================
            // STEP 2: EXCHANGE SECRET KEY FOR USER BIODATA
            // ==========================================
            $bioResponse = Http::asForm()
                ->timeout(10)
                ->withoutVerifying()
                ->withHeaders(['User-Agent' => 'Student Login System'])
                ->post($apiUrl, [
                    'act'       => 'GetBiodata',
                    'secretkey' => $secretKey,
                ]);

            if ($bioResponse->failed()) {
                return back()->with('flash_message', [
                    'display' => 'modal',
                    'type'    => 'error',
                    'title'   => 'Fetch Data Failed',
                    'message' => 'Failed to retrieve your profile information records from the central server.'
                ]);
            }

            $bioResult = $bioResponse->json();

            if (!isset($bioResult['error_code']) || $bioResult['error_code'] != 0) {
                return back()->with('flash_message', [
                    'display' => 'modal',
                    'type'    => 'error',
                    'title'   => 'Invalid Data Structure',
                    'message' => 'The profile response data from the central server was unrecognized.'
                ]);
            }

            $userData = $bioResult['data'] ?? null;

            if (isset($userData['role']) && $userData['role'] === 'Mahasiswa') {

                $nik     = $userData['id'];
                $nama    = $userData['nama'];
                $email   = $userData['email'];
                $prodi   = $userData['prodi'];
                $jurusan = $userData['jurusan'] ?? '-';

                // Just-In-Time Provisioning
                $student = Student::find($nik);

                if (!$student) {
                    $student = Student::create([
                        'id_mhs'  => $nik,
                        'name'    => $nama,
                        'prodi'   => $prodi,
                        'jurusan' => $jurusan,
                    ]);

                    $student->profile()->create([
                        'profile'         => '-',
                        'gpa'             => '0.00',
                        'personal_email'  => $email,
                        'profile_picture' => 'default.jpg',
                        'address'         => '-',
                        'phone'           => '-',
                        'skill'           => '-',
                    ]);
                }

                session([
                    'user_nik'     => $nik,
                    'user_jabatan' => $userData['role'],
                    'nama'         => $nama,
                    'email'        => $email,
                ]);

                return redirect()->route('dashboard')->with('flash_message', [
                    'display' => 'toast',
                    'type'    => 'success',
                    'message' => 'Welcome to the TalentHub portfolio workspace!'
                ]);
            }

            Log::warning("Student Auth Access Denied / Non-Student Login", [
                'input_username' => $request->username,
                'api_response_data' => $userData
            ]);

            return back()->with('flash_message', [
                'display' => 'modal',
                'type'    => 'error',
                'title'   => 'Access Denied',
                'message' => 'Sign In Failed. You do not possess the required student credentials to enter this portal.'
            ]);

        } catch (\Exception $e) {
            Log::error("Log Student Auth Multi-Step Failed", [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine()
            ]);

            return back()->with('flash_message', [
                'display' => 'modal',
                'type'    => 'error',
                'title'   => 'System Error',
                'message' => 'An internal backend interruption error occurred: ' . $e->getMessage(),
            ]);
        }
    
        }
    
    public function showIndustryLogin()
    {
        return Inertia::render('Auth/LoginIndustry');
    }

    public function attemptIndustryLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Mencari user berdasarkan username di tabel user_company
        $userCompany = DB::table('user_company')->where('username', $request->username)->first();

        if ($userCompany) {
            if (Hash::check($request->password, $userCompany->password)) {
                session([
                    'username'        => $userCompany->username,
                    'user_fullname'   => $userCompany->user_fullname,
                    'id_user_company' => $userCompany->id_user_company,
                ]);

                return redirect()->route('dashboard')->with('flash_message', [
                    'display' => 'toast',
                    'type'    => 'success',
                    'message' => 'Welcome corporate partner! Successfully logged in.'
                ]);
            }

            return back()->with('flash_message', [
                'display' => 'modal',
                'type'    => 'error',
                'title'   => 'Sign In Failed',
                'message' => 'The provided corporate partner account password is incorrect.'
            ]);
        }

        return back()->with('flash_message', [
            'display' => 'modal',
            'type'    => 'error',
            'title'   => 'Sign In Failed',
            'message' => 'This corporate entity or industry account registration was not found.'
        ]);
    }

    public function logout(Request $request)
    {
        $request->session()->flush();

        return redirect()->route('landing')->with('flash_message', [
            'display' => 'toast',
            'type'    => 'success',
            'message' => 'You have been safely signed out of the TalentHub ecosystem.'
        ]);
    }

    // TARUH DI SINI (Mulai Baris 240)
    public function showIndustryRegister()
    {
        return Inertia::render('Auth/RegisterIndustry');
    }

    public function registerIndustry(Request $request)
    {
        $request->validate([
            'username'      => 'required|string|unique:user_company,username|max:50',
            'user_fullname' => 'required|string|max:255',
            'password'      => 'required|string|min:6|confirmed',
        ]);

        DB::table('user_company')->insert([
            'username'      => $request->username,
            'user_fullname' => $request->user_fullname,
            'password'      => Hash::make($request->password),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return redirect()->route('login.industry')->with('flash_message', [
            'display' => 'toast',
            'type'    => 'success',
            'message' => 'Account created successfully! Please sign in with your credentials.'
        ]);
    }
}