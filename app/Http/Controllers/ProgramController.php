<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    // List all programs
    public function index()
    {
        $programs = Program::all();
        return response()->json($programs);
    }

    // Store a new program
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:programs,name',
            'description' => 'nullable|string',
        ]);

        $program = Program::create($validated);

        return response()->json(['message' => 'Program created successfully.', 'program' => $program], 201);
    }

    // Show a single program
    public function show($id)
    {
        $program = Program::findOrFail($id);
        return response()->json($program);
    }

    // Update a program
    public function update(Request $request, $id)
    {
        $program = Program::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $program->update($validated);

        return response()->json(['message' => 'Program updated successfully.', 'program' => $program]);
    }

    // Delete a program
    public function destroy($id)
    {
        $program = Program::findOrFail($id);
        $program->delete();

        return response()->json(['message' => 'Program deleted successfully.']);
    }
}
