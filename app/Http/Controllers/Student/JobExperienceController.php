<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\JobExperience;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class JobExperienceController extends Controller
{
    public function index()
    {
        $nik = session('user_nik');
        if (!$nik) return redirect()->route('login.student');

        $student = Student::find($nik);
        $experiences = JobExperience::where('id_mhs', $nik)->orderBy('start_date', 'desc')->get();

        return Inertia::render('Student/Experience/Index', [
            'student' => $student,
            'experiences' => $experiences
        ]);
    }

    public function store(Request $request)
    {
        Log::info("=== STORE JOB EXPERIENCE START ===", $request->all());
        $nik = session('user_nik');

        // Modifikasi payload: jika is_current sejati (true), paksa end_date bernilai null
        if ($request->input('is_current') === true) {
            $request->merge(['end_date' => null]);
        }

        $validator = Validator::make($request->all(), [
            'title'           => 'required|string|max:150',
            'company'         => 'required|string|max:150',
            'location'        => 'required|string|max:250',
            'employment_type' => 'required|string|max:200',
            'start_date'      => 'required|date|before_or_equal:today', // 🌟 Batasi maksimal hari ini
            'end_date'        => 'required_without:is_current|nullable|date|after_or_equal:start_date|before_or_equal:today', // 🌟 Wajib diisi HANYA JIKA tidak sedang aktif bekerja
        ], [
            'title.required'           => 'The job title field is required.',
            'company.required'         => 'The company name field cannot be empty.',
            'start_date.before_or_equal'=> 'Commencement date cannot be a future date.',
            'end_date.required_without'=> 'Termination date is required unless you are currently working in this role.',
            'end_date.before_or_equal' => 'Termination date cannot be a future date.',
            'end_date.after_or_equal'  => 'The end date cannot be earlier than the start date.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        try {
            JobExperience::create([
                'id_mhs'          => $nik,
                'title'           => $request->title,
                'company'         => $request->company,
                'location'        => $request->location,
                'employment_type' => $request->employment_type,
                'start_date'      => $request->start_date,
                'end_date'        => $request->end_date, // Akan tersimpan sebagai null di DB jika di-check masih aktif
            ]);

            return back()->with('flash_message', [
                'display' => 'toast', 'type' => 'success', 'message' => 'Professional experience successfully added!'
            ]);
        } catch (\Exception $e) {
            Log::error("Store Job Experience Failed: " . $e->getMessage());
            return back()->with('flash_message', [
                'display' => 'modal', 'type' => 'error', 'title' => 'Save Failure', 'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        Log::info("=== UPDATE JOB EXPERIENCE START ID: {$id} ===", $request->all());

        // 🌟 PERBAIKAN 1: Jika is_current bernilai true, paksa end_date bernilai null sebelum validasi
        if ($request->input('is_current') === true) {
            $request->merge(['end_date' => null]);
        }

        // 🌟 PERBAIKAN 2: Gunakan aturan strict english validation yang membatasi future date dan mendeteksi is_current
        $validator = Validator::make($request->all(), [
            'title'           => 'required|string|max:150',
            'company'         => 'required|string|max:150',
            'location'        => 'required|string|max:250',
            'employment_type' => 'required|string|max:200',
            'start_date'      => 'required|date|before_or_equal:today',
            'end_date'        => 'required_without:is_current|nullable|date|after_or_equal:start_date|before_or_equal:today',
        ], [
            'title.required'            => 'The job title field is required.',
            'company.required'          => 'The company name field cannot be empty.',
            'start_date.before_or_equal' => 'Commencement date cannot be a future date.',
            'end_date.required_without' => 'Termination date is required unless you are currently working in this role.',
            'end_date.before_or_equal'  => 'Termination date cannot be a future date.',
            'end_date.after_or_equal'   => 'The end date cannot be earlier than the start date.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        try {
            // 🌟 PERBAIKAN 3: Melakukan pembaharuan record data secara presisi
            JobExperience::findOrFail($id)->update([
                'title'           => $request->title,
                'company'         => $request->company,
                'location'        => $request->location,
                'employment_type' => $request->employment_type,
                'start_date'      => $request->start_date,
                'end_date'        => $request->end_date, // Menyimpan nilai null dengan aman jika checkbox ongoing aktif
            ]);

            return back()->with('flash_message', [
                'display' => 'toast',
                'type'    => 'success',
                'message' => 'Experience details successfully synchronized!'
            ]);
        } catch (\Exception $e) {
            Log::error("Update Job Experience Failed: " . $e->getMessage());
            return back()->with('flash_message', [
                'display' => 'modal',
                'type'    => 'error',
                'title'   => 'Modification Failure',
                'message' => 'An error occurred while updating records: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            JobExperience::destroy($id);
            return back()->with('flash_message', [
                'display' => 'toast', 'type' => 'success', 'message' => 'Experience entry has been removed.'
            ]);
        } catch (\Exception $e) {
            Log::error("Destroy Job Experience Failed: " . $e->getMessage());
            return back()->with('flash_message', [
                'display' => 'modal', 'type' => 'error', 'title' => 'Removal Failure', 'message' => 'Failed to discard data asset.'
            ]);
        }
    }
}
