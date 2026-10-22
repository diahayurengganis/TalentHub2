<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AchievementController extends Controller
{
    public function index()
    {
        $nik = session('user_nik');
        if (!$nik) return redirect()->route('login.student');

        $student = Student::find($nik);
        $achievements = Achievement::where('id_mhs', $nik)->orderBy('time', 'desc')->get();

        return Inertia::render('Student/Achievement/Index', [
            'student' => $student,
            'achievements' => $achievements
        ]);
    }

    public function store(Request $request)
    {
        Log::info("=== STORE ACHIEVEMENT START ===", $request->all());
        $nik = session('user_nik');

        $validator = Validator::make($request->all(), [
            'achievement'  => 'required|string|max:200',
            'title'        => 'required|string|max:200',
            'category'     => 'required|string',
            'level'        => 'required|string',
            'time'         => 'required|date',
            'organization' => 'required|string|max:200',
            'location'     => 'required|string|max:200',
            'description'  => 'required|string|max:255',
            'participant'  => 'nullable|integer|min:1',
            'attachment'   => 'nullable|url|max:255',
        ], [
            'achievement.required'  => 'The award placement distinction (e.g. 1st Winner) is required.',
            'title.required'        => 'The competition or event title cannot be empty.',
            'category.required'     => 'An activity category track must be chosen.',
            'level.required'        => 'A regional scope level must be selected.',
            'time.required'         => 'The award or charter date field is required.',
            'organization.required' => 'The organizing committee or host field is required.',
            'location.required'     => 'The venue city location is required.',
            'description.required'  => 'A brief description of your accomplishment is required.',
            'attachment.url'        => 'The certificate link must start with a valid http:// or https:// structure.',
        ]);

        if ($validator->fails()) {
            Log::warning("Achievement Request Validation Faulted", $validator->errors()->toArray());
            throw new ValidationException($validator);
        }

        try {
            Achievement::create([
                'id_mhs'        => $nik,
                'achievement'   => $request->achievement,
                'title'         => $request->title,
                'category'      => $request->category,
                'level'         => $request->level,
                'time'          => $request->time,
                'organization'  => $request->organization,
                'location'      => $request->location,
                'description'   => $request->description,
                'participant'   => $request->participant,
                'attachment'    => $request->attachment ?? '-',
                'documentation' => '-', // Database default string padding fallback
            ]);

            Log::info("Achievement successfully recorded for ID: " . $nik);
            return back()->with('flash_message', [
                'display' => 'toast',
                'type' => 'success',
                'message' => 'Achievement record successfully appended!'
            ]);
        } catch (\Exception $e) {
            Log::error("Store Achievement Module Exception Crash: " . $e->getMessage());
            return back()->with('flash_message', [
                'display' => 'modal',
                'type' => 'error',
                'title' => 'Save Failure',
                'message' => 'An internal database save interception error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        Log::info("=== UPDATE ACHIEVEMENT START ID: {$id} ===", $request->all());

        $validator = Validator::make($request->all(), [
            'achievement'  => 'required|string|max:200',
            'title'        => 'required|string|max:200',
            'category'     => 'required|string',
            'level'        => 'required|string',
            'time'         => 'required|date',
            'organization' => 'required|string|max:200',
            'location'     => 'required|string|max:200',
            'description'  => 'required|string|max:255',
            'participant'  => 'nullable|integer|min:1',
            'attachment'   => 'nullable|url|max:255',
        ], [
            'achievement.required' => 'The award placement distinction field is required.',
            'title.required'        => 'The competition or event title field is required.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        try {
            Achievement::where('id', $id)->update([
                'achievement'  => $request->achievement,
                'title'        => $request->title,
                'category'     => $request->category,
                'level'        => $request->level,
                'time'         => $request->time,
                'organization' => $request->organization,
                'location'     => $request->location,
                'description'  => $request->description,
                'participant'  => $request->participant,
                'attachment'   => $request->attachment ?? '-',
            ]);

            return back()->with('flash_message', [
                'display' => 'toast',
                'type' => 'success',
                'message' => 'Achievement records successfully synchronized!'
            ]);
        } catch (\Exception $e) {
            Log::error("Update Achievement Record Module Failed: " . $e->getMessage());
            return back()->with('flash_message', [
                'display' => 'modal',
                'type' => 'error',
                'title' => 'Modification Failure',
                'message' => 'Failed to update the specified achievement attributes entry.'
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            Achievement::destroy($id);
            return back()->with('flash_message', [
                'display' => 'toast',
                'type' => 'success',
                'message' => 'The target achievement history entry has been removed.'
            ]);
        } catch (\Exception $e) {
            Log::error("Destroy Achievement Record Module Failed: " . $e->getMessage());
            return back()->with('flash_message', [
                'display' => 'modal',
                'type' => 'error',
                'title' => 'Removal Failure',
                'message' => 'The target asset record could not be discarded from the system database.'
            ]);
        }
    }
}
