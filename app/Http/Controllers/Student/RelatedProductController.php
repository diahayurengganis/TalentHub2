<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\RelatedProduct;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class RelatedProductController extends Controller
{
    /**
     * Display a listing of the claimed products.
     */
    public function index()
    {
        $nik = session('user_nik');
        if (!$nik) return redirect()->route('login.student');

        $student = Student::find($nik);
        $products = RelatedProduct::where('id_mhs', $nik)->orderBy('id', 'desc')->get();

        return Inertia::render('Student/Product/Index', [
            'student' => $student,
            'products' => $products
        ]);
    }

    /**
     * Store a newly created product artifact entry.
     */
    public function store(Request $request)
    {
        Log::info("=== STORE RELATED PRODUCT START ===", $request->all());
        $nik = session('user_nik');

        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:150',
            'description' => 'required|string|max:500',
            'link'        => 'required|url|max:255',
        ], [
            'title.required'       => 'The product or application name title field is required.',
            'description.required' => 'A brief overview description of your project product is required.',
            'link.required'        => 'A functional production deployment or repository URL link is required.',
            'link.url'             => 'The link must start with a valid http:// or https:// structure.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        try {
            RelatedProduct::create([
                'id_mhs'      => $nik,
                'title'       => $request->title,
                'description' => $request->description,
                'link'        => $request->link,
                'thumbnail'   => '-', // Set default dash string placeholder
            ]);

            return back()->with('flash_message', [
                'display' => 'toast',
                'type' => 'success',
                'message' => 'Project artifact successfully pinned to your resume portfolio!'
            ]);
        } catch (\Exception $e) {
            Log::error("Store Related Product Failed: " . $e->getMessage());
            return back()->with('flash_message', [
                'display' => 'modal',
                'type' => 'error',
                'title' => 'Save Failure',
                'message' => 'An internal database exception occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update the specified product entry.
     */
    public function update(Request $request, $id)
    {
        Log::info("=== UPDATE RELATED PRODUCT START ID: {$id} ===", $request->all());

        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:150',
            'description' => 'required|string|max:500',
            'link'        => 'required|url|max:255',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        try {
            RelatedProduct::findOrFail($id)->update([
                'title'       => $request->title,
                'description' => $request->description,
                'link'        => $request->link,
            ]);

            return back()->with('flash_message', [
                'display' => 'toast',
                'type' => 'success',
                'message' => 'Product matrix logs successfully synchronized!'
            ]);
        } catch (\Exception $e) {
            Log::error("Update Related Product Failed: " . $e->getMessage());
            return back()->with('flash_message', [
                'display' => 'modal',
                'type' => 'error',
                'title' => 'Modification Failure',
                'message' => 'Failed to synchronize product properties attributes.'
            ]);
        }
    }

    /**
     * Remove the specified product entry from storage.
     */
    public function destroy($id)
    {
        try {
            RelatedProduct::destroy($id);
            return back()->with('flash_message', [
                'display' => 'toast',
                'type' => 'success',
                'message' => 'The target project portfolio asset has been removed.'
            ]);
        } catch (\Exception $e) {
            Log::error("Destroy Related Product Failed: " . $e->getMessage());
            return back()->with('flash_message', [
                'display' => 'modal',
                'type' => 'error',
                'title' => 'Removal Failure',
                'message' => 'Failed to drop data asset from system tables.'
            ]);
        }
    }
}
