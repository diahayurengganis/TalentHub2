<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class LanguageController extends Controller
{
    public function index()
    {
        $nik = session('user_nik');
        if (!$nik) return redirect()->route('login.student');

        $student = Student::find($nik);

        // Memuat data language beserta relasi attachments-nya
        $languages = Language::with('attachments')
            ->where('id_mhs', $nik)
            ->orderBy('id', 'desc')
            ->get();

        return Inertia::render('Student/Language/Index', [
            'student' => $student,
            'languages' => $languages
        ]);
    }

    public function store(Request $request)
    {
        Log::info("=== STORE LANGUAGE START ===", $request->all());
        $nik = session('user_nik');

        $validator = Validator::make($request->all(), [
            'language'    => 'required|string|max:50',
            'is_active'   => 'required|boolean',
            'score'       => 'required|string|max:50',
            'certificate' => 'required|url|max:255',
            'attachment'  => 'required|url|max:255',
        ], [
            'language.required'    => 'The language field is required.',
            'certificate.url'      => 'The certificate link must start with a valid http:// or https:// structure.',
            'attachment.url'       => 'The attachment link must start with a valid http:// or https:// structure.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        DB::beginTransaction();
        try {
            // 1. Simpan ke tabel induk (language)
            $language = Language::create([
                'id_mhs'    => $nik,
                'language'  => $request->language,
                'is_active' => $request->is_active,
            ]);

            // 2. Simpan ke tabel relasi (attach_language) via HasMany relation method
            $language->attachments()->create([
                'score'       => $request->score,
                'certificate' => $request->certificate,
                'attachment'  => $request->attachment,
            ]);

            DB::commit();
            return back()->with('flash_message', [
                'display' => 'toast', 'type' => 'success', 'message' => 'Language competency successfully appended!'
              ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Store Language Failed: " . $e->getMessage());
            return back()->with('flash_message', [
                'display' => 'modal', 'type' => 'error', 'title' => 'Save Failure', 'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        Log::info("=== UPDATE LANGUAGE START ID: {$id} ===", $request->all());

        $validator = Validator::make($request->all(), [
            'language'    => 'required|string|max:50',
            'is_active'   => 'required|boolean',
            'score'       => 'required|string|max:50',
            'certificate' => 'required|url|max:255',
            'attachment'  => 'required|url|max:255',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        DB::beginTransaction();
        try {
            // 1. Update tabel induk
            $language = Language::findOrFail($id);
            $language->update([
                'language'  => $request->language,
                'is_active' => $request->is_active,
            ]);

            // 2. Update atau create tabel anak jika belum ada
            $language->attachments()->updateOrCreate(
                ['language_id' => $id],
                [
                    'score'       => $request->score,
                    'certificate' => $request->certificate,
                    'attachment'  => $request->attachment,
                ]
            );

            DB::commit();
            return back()->with('flash_message', [
                'display' => 'toast', 'type' => 'success', 'message' => 'Language records successfully synchronized!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Update Language Failed: " . $e->getMessage());
            return back()->with('flash_message', [
                'display' => 'modal', 'type' => 'error', 'title' => 'Modification Failure', 'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            // Cascading delete akan otomatis berjalan jika diset di migration DB,
            // Jika tidak, Eloquent akan menghapusnya secara aman.
            Language::destroy($id);
            return back()->with('flash_message', [
                'display' => 'toast', 'type' => 'success', 'message' => 'The target language capability record has been removed.'
            ]);
        } catch (\Exception $e) {
            Log::error("Destroy Language Failed: " . $e->getMessage());
            return back()->with('flash_message', [
                'display' => 'modal', 'type' => 'error', 'title' => 'Removal Failure', 'message' => 'Failed to discard data.'
            ]);
        }
    }
}
