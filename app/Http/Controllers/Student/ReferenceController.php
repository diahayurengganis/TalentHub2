<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Reference;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ReferenceController extends Controller
{
    /**
     * Display a listing of the student's professional references.
     */
    public function index()
    {
        $nik = session('user_nik');
        if (!$nik) return redirect()->route('login.student');

        $student = Student::find($nik);
        $references = Reference::where('id_mhs', $nik)->orderBy('id', 'desc')->get();

        return Inertia::render('Student/Reference/Index', [
            'student' => $student,
            'references' => $references
        ]);
    }

    /**
     * Store a newly created professional reference.
     */
    public function store(Request $request)
    {
        Log::info("=== STORE REFERENCE START ===", $request->all());
        $nik = session('user_nik');

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:150',
            'title'       => 'required|string|max:150',
            'institution' => 'required|string|max:200',
            'email'       => 'required|email|max:150',
            'telp'        => 'required|string|max:30',
        ], [
            'name.required'        => 'The referee name field is required.',
            'title.required'       => 'The professional designation/title field is required.',
            'institution.required' => 'The company or university institution name is required.',
            'email.required'       => 'A valid institutional communication email is required.',
            'telp.required'        => 'A reachability telephone/WhatsApp number is required.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        try {
            Reference::create([
                'id_mhs'      => $nik,
                'name'        => $request->name,
                'title'       => $request->title,
                'institution' => $request->institution,
                'email'       => $request->email,
                'telp'        => $request->telp,
            ]);

            return back()->with('flash_message', [
                'display' => 'toast',
                'type' => 'success',
                'message' => 'Professional reference person successfully appended!'
            ]);
        } catch (\Exception $e) {
            Log::error("Store Reference Failed: " . $e->getMessage());
            return back()->with('flash_message', [
                'display' => 'modal',
                'type' => 'error',
                'title' => 'Save Failure',
                'message' => 'An internal database exception occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update the specified reference entry.
     */
    public function update(Request $request, $id)
    {
        Log::info("=== UPDATE REFERENCE START ID: {$id} ===", $request->all());

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:150',
            'title'       => 'required|string|max:150',
            'institution' => 'required|string|max:200',
            'email'       => 'required|email|max:150',
            'telp'        => 'required|string|max:30',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        try {
            Reference::findOrFail($id)->update([
                'name'        => $request->name,
                'title'       => $request->title,
                'institution' => $request->institution,
                'email'       => $request->email,
                'telp'        => $request->telp,
            ]);

            return back()->with('flash_message', [
                'display' => 'toast',
                'type' => 'success',
                'message' => 'Reference metadata successfully synchronized!'
            ]);
        } catch (\Exception $e) {
            Log::error("Update Reference Failed: " . $e->getMessage());
            return back()->with('flash_message', [
                'display' => 'modal',
                'type' => 'error',
                'title' => 'Modification Failure',
                'message' => 'Failed to synchronize reference parameters attributes.'
            ]);
        }
    }

    /**
     * Remove the specified reference record.
     */
    public function destroy($id)
    {
        try {
            Reference::destroy($id);
            return back()->with('flash_message', [
                'display' => 'toast',
                'type' => 'success',
                'message' => 'The professional reference contact has been safely removed.'
            ]);
        } catch (\Exception $e) {
            Log::error("Destroy Reference Failed: " . $e->getMessage());
            return back()->with('flash_message', [
                'display' => 'modal',
                'type' => 'error',
                'title' => 'Removal Failure',
                'message' => 'Failed to drop data asset from system tables.'
            ]);
        }
    }
}
