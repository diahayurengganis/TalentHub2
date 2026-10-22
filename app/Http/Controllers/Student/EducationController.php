<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Education;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class EducationController extends Controller
{
    /**
     * Display a listing of the student's education history entries.
     */
    public function index()
    {
        $nik = session('user_nik');
        if (!$nik) {
            return redirect()->route('login.student');
        }

        $student = Student::find($nik);
        $educations = Education::where('id_mhs', $nik)->orderBy('year_start', 'desc')->get();

        return Inertia::render('Student/Education/Index', [
            'student' => $student,
            'educations' => $educations
        ]);
    }

    /**
     * Store a newly created education history record into storage.
     */
    public function store(Request $request)
    {
        Log::info("=== STORE EDUCATION START ===", $request->all());
        $nik = session('user_nik');

        $validator = Validator::make($request->all(), [
            'level'       => 'required|string|max:50',
            'institution' => 'required|string|max:150',
            'year_start'  => 'required|digits:4|integer',
            'year_end'    => 'required|digits:4|integer|gte:year_start',
            'major'       => 'nullable|string|max:100',
        ], [
            'level.required'       => 'The education degree level is required.',
            'institution.required' => 'The institution or school name field cannot be empty.',
            'year_start.required'  => 'The start year field is required.',
            'year_start.digits'    => 'The start year format must be exactly 4 digits.',
            'year_end.required'    => 'The graduation year field is required.',
            'year_end.digits'      => 'The graduation year format must be exactly 4 digits.',
            'year_end.gte'         => 'The graduation year cannot be earlier than the start year.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        try {
            Education::create([
                'id_mhs'      => $nik,
                'level'       => $request->level,
                'institution' => $request->institution,
                'year_start'  => $request->year_start,
                'year_end'    => $request->year_end,
                'major'       => $request->major ?? '-',
            ]);

            return back()->with('flash_message', [
                'display' => 'toast',
                'type' => 'success',
                'message' => 'Education record successfully appended!'
            ]);
        } catch (\Exception $e) {
            Log::error("Store Education Record Module Failed: " . $e->getMessage());
            return back()->with('flash_message', [
                'display' => 'modal',
                'type' => 'error',
                'title' => 'Save Failure',
                'message' => 'An internal database save interception error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update the specified education history record in storage.
     */
    public function update(Request $request, $id)
    {
        Log::info("=== UPDATE EDUCATION START ID: {$id} ===", $request->all());

        $validator = Validator::make($request->all(), [
            'level'       => 'required|string|max:50',
            'institution' => 'required|string|max:150',
            'year_start'  => 'required|digits:4|integer',
            'year_end'    => 'required|digits:4|integer|gte:year_start',
            'major'       => 'nullable|string|max:100',
        ], [
            'level.required'       => 'The education degree level is required.',
            'institution.required' => 'The institution or school name field cannot be empty.',
            'year_start.required'  => 'The start year field is required.',
            'year_end.required'    => 'The graduation year field is required.',
            'year_end.gte'         => 'The graduation year cannot be earlier than the start year.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        try {
            Education::where('id', $id)->update([
                'level'       => $request->level,
                'institution' => $request->institution,
                'year_start'  => $request->year_start,
                'year_end'    => $request->year_end,
                'major'       => $request->major ?? '-',
            ]);

            return back()->with('flash_message', [
                'display' => 'toast',
                'type' => 'success',
                'message' => 'Education records successfully synchronized!'
            ]);
        } catch (\Exception $e) {
            Log::error("Update Education Record Module Failed: " . $e->getMessage());
            return back()->with('flash_message', [
                'display' => 'modal',
                'type' => 'error',
                'title' => 'Modification Failure',
                'message' => 'Failed to update the specified education attributes data entry.'
            ]);
        }
    }

    /**
     * Remove the specified education history record from storage.
     */
    public function destroy($id)
    {
        try {
            Education::destroy($id);
            return back()->with('flash_message', [
                'display' => 'toast',
                'type' => 'success',
                'message' => 'The target education history record has been removed.'
            ]);
        } catch (\Exception $e) {
            Log::error("Destroy Education Record Module Failed: " . $e->getMessage());
            return back()->with('flash_message', [
                'display' => 'modal',
                'type' => 'error',
                'title' => 'Removal Failure',
                'message' => 'The target asset record could not be discarded from the system database.'
            ]);
        }
    }
}
