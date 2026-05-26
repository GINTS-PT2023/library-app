<?php

namespace App\Http\Controllers;

use App\Models\Reader;
use Illuminate\Http\Request;

class ReaderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Reader::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:readers',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        return Reader::create($validated);
    }

    public function show(Reader $reader)
    {
        return $reader;
    }

    public function update(Request $request, Reader $reader)
    {
        $validated = $request->validate([
            'first_name' => 'string',
            'last_name' => 'string',
            'email' => 'email|unique:readers,email,'.$reader->id,
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $reader->update($validated);

        return $reader;
    }

    public function destroy(Reader $reader)
    {
        $reader->delete();

        return response()->noContent();
    }
}
