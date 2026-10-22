<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class CertificationController extends Controller
{
    public function index()
    {
        $nik = session('user_nik');
        if (!$nik) return redirect()->route('login.student');

        $student = Student::find($nik);
        // Mengurutkan berdasarkan tanggal terbit terbaru
        $certifications = Certification::where('id_mhs', $nik)->orderBy('start_date', 'desc')->get();

        return Inertia::render('Student/Certification/Index', [
            'student' => $student,
            'certifications' => $certifications
        ]);
    }

    public function store(Request $request)
    {
        Log::info("=== STORE CERTIFICATION START ===", $request->all());
        $nik = session('user_nik');

        // Jika diset seumur hidup (is_lifetime), paksa end_date bernilai null sebelum validasi
        if ($request->input('is_lifetime') === true) {
            $request->merge(['end_date' => null]);
        }

        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:200',
            'institution' => 'required|string|max:200',
            'description' => 'required|string|max:400',
            'start_date'  => 'required|date|before_or_equal:today',
            'end_date'    => 'required_without:is_lifetime|nullable|date|after_or_equal:start_date',
            'attachment'  => 'nullable|url|max:255',
        ], [
            'title.required'            => 'The certificate designation title field is required.',
            'institution.required'      => 'The issuing authority institution is required.',
            'description.required'      => 'A brief core syllabus description is required.',
            'start_date.before_or_equal' => 'The issuance date cannot be a future date.',
            'end_date.required_without' => 'Termination date is required unless this certification has no expiration date.',
            'end_date.after_or_equal'   => 'The expiration date cannot be earlier than the issuance date.',
            'attachment.url'            => 'The digital validation link must start with http:// or https://',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        try {
            Certification::create([
                'id_mhs'      => $nik,
                'title'       => $request->title,
                'institution' => $request->institution,
                'description' => $request->description,
                'start_date'  => $request->start_date,
                'end_date'    => $request->end_date,
                'attachment'  => $request->attachment ?? '-',
            ]);

            return back()->with('flash_message', [
                'display' => 'toast', 'type' => 'success', 'message' => 'Certification successfully appended!'
            ]);
        } catch (\Exception $e) {
            Log::error("Store Certification Failed: " . $e->getMessage());
            return back()->with('flash_message', [
                'display' => 'modal', 'type' => 'error', 'title' => 'Save Failure', 'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        Log::info("=== UPDATE CERTIFICATION START ID: {$id} ===", $request->all());

        if ($request->input('is_lifetime') === true) {
            $request->merge(['end_date' => null]);
        }

        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:200',
            'institution' => 'required|string|max:200',
            'description' => 'required|string|max:400',
            'start_date'  => 'required|date|before_or_equal:today',
            'end_date'    => 'required_without:is_lifetime|nullable|date|after_or_equal:start_date',
            'attachment'  => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        try {
            Certification::findOrFail($id)->update([
                'title'       => $request->title,
                'institution' => $request->institution,
                'description' => $request->description,
                'start_date'  => $request->start_date,
                'end_date'    => $request->end_date,
                'attachment'  => $request->attachment ?? '-',
            ]);

            return back()->with('flash_message', [
                'display' => 'toast', 'type' => 'success', 'message' => 'Certification details successfully synchronized!'
            ]);
        } catch (\Exception $e) {
            Log::error("Update Certification Failed: " . $e->getMessage());
            return back()->with('flash_message', [
                'display' => 'modal', 'type' => 'error', 'title' => 'Modification Failure', 'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            Certification::destroy($id);
            return back()->with('flash_message', [
                'display' => 'toast', 'type' => 'success', 'message' => 'The certification record has been removed.'
            ]);
        } catch (\Exception $e) {
            Log::error("Destroy Certification Failed: " . $e->getMessage());
            return back()->with('flash_message', [
                'display' => 'modal', 'type' => 'error', 'title' => 'Removal Failure', 'message' => 'Failed to discard asset.'
            ]);
        }
    }
}
