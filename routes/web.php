<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Landing\WelcomeController;
use App\Http\Controllers\Student\AchievementController;
use App\Http\Controllers\Student\CertificationController;
use App\Http\Controllers\Student\EducationController;
use App\Http\Controllers\Student\JobExperienceController;
use App\Http\Controllers\Student\LanguageController;
use App\Http\Controllers\Student\ReferenceController;
use App\Http\Controllers\Student\RelatedProductController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\TU\TUController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Landing Page
Route::get('/', [WelcomeController::class, 'index'])->name('landing');
Route::post('/contact-send', [WelcomeController::class, 'sendContact'])->name('contact.send');

// Otentikasi Mahasiswa
Route::get('/login/student', [AuthController::class, 'showStudentLogin'])->name('login.student');
Route::post('/login/student', [AuthController::class, 'attemptStudentLogin'])->name('login.attempt');

// Otentikasi Industri / Perusahaan
Route::get('/login/industry', [AuthController::class, 'showIndustryLogin'])->name('login.industry');
Route::post('/login/industry', [AuthController::class, 'attemptIndustryLogin'])->name('login.industry.attempt');

// --- REGISTRASI INDUSTRI ---
Route::get('/register-industry', [AuthController::class, 'showIndustryRegister'])->name('register.industry');
Route::post('/register-industry', [AuthController::class, 'registerIndustry']);

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// PORTAL PENCARIAN & EKSPLORASI MAHASISWA
// ==========================================
Route::get('/students', [StudentController::class, 'index'])->name('students.index');
Route::get('/students/{id_mhs}', [StudentController::class, 'show'])->name('students.show');

// ==========================================
// PORTAL MANAGEMENT / CRUD DATA DIRI (Dashboard Mahasiswa)
// ==========================================
Route::prefix('dashboard/student')->group(function () {
    Route::get('/', [StudentProfileController::class, 'index'])->name('dashboard');

    Route::post('/profile', [StudentProfileController::class, 'updateProfile'])->name('profile.update');
    Route::delete('/profile/picture', [StudentProfileController::class, 'destroyProfilePicture'])->name('profile.picture.destroy');

    Route::get('/education', [EducationController::class, 'index'])->name('education.index');
    Route::post('/education', [EducationController::class, 'store'])->name('education.store');
    Route::put('/education/{id}', [EducationController::class, 'update'])->name('education.update');
    Route::delete('/education/{id}', [EducationController::class, 'destroy'])->name('education.destroy');

    Route::get('/achievement', [AchievementController::class, 'index'])->name('achievement.index');
    Route::post('/achievement', [AchievementController::class, 'store'])->name('achievement.store');
    Route::put('/achievement/{id}', [AchievementController::class, 'update'])->name('achievement.update');
    Route::delete('/achievement/{id}', [AchievementController::class, 'destroy'])->name('achievement.destroy');

    Route::get('/languages', [LanguageController::class, 'index'])->name('languages.index');
    Route::post('/languages', [LanguageController::class, 'store'])->name('languages.store');
    Route::put('/languages/{id}', [LanguageController::class, 'update'])->name('languages.update');
    Route::delete('/languages/{id}', [LanguageController::class, 'destroy'])->name('languages.destroy');

    Route::get('/career/references', [ReferenceController::class, 'index'])->name('references.index');
    Route::post('/career/references', [ReferenceController::class, 'store'])->name('references.store');
    Route::put('/career/references/{id}', [ReferenceController::class, 'update'])->name('references.update');
    Route::delete('/career/references/{id}', [ReferenceController::class, 'destroy'])->name('references.destroy');

    Route::get('/career/experiences', [JobExperienceController::class, 'index'])->name('experiences.index');
    Route::post('/career/experiences', [JobExperienceController::class, 'store'])->name('experiences.store');
    Route::put('/career/experiences/{id}', [JobExperienceController::class, 'update'])->name('experiences.update');
    Route::delete('/career/experiences/{id}', [JobExperienceController::class, 'destroy'])->name('experiences.destroy');

    Route::get('/career/certifications', [CertificationController::class, 'index'])->name('certifications.index');
    Route::post('/career/certifications', [CertificationController::class, 'store'])->name('certifications.store');
    Route::put('/career/certifications/{id}', [CertificationController::class, 'update'])->name('certifications.update');
    Route::delete('/career/certifications/{id}', [CertificationController::class, 'destroy'])->name('certifications.destroy');

    Route::get('/artifacts/products', [RelatedProductController::class, 'index'])->name('products.index');
    Route::post('/artifacts/products', [RelatedProductController::class, 'store'])->name('products.store');
    Route::put('/artifacts/products/{id}', [RelatedProductController::class, 'update'])->name('products.update');
    Route::delete('/artifacts/products/{id}', [RelatedProductController::class, 'destroy'])->name('products.destroy');
});

// ====================
// TATA USAHA (TU) & ADMIN DASHBOARD
// ====================
Route::get('/dashboard', function () {
    return redirect()->route('tu.verifications.index');
})->name('dashboard');

Route::get('/dashboard/tu/verifications', [TUController::class, 'verificationIndex'])->name('tu.verifications.index');
Route::put('/dashboard/tu/verifications/{id}', [TUController::class, 'updateVerification'])->name('tu.verifications.update');