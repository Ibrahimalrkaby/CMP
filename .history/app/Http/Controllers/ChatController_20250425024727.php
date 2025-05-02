<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('teacher')->only('create');
    }

    public function index()
    {
        return Chat::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'course_id' => 'required|exists:courses,id',
        ]);

        $message = Chat::create($validated);

        return response()->json([
            'message' => 'Message created successfully',
            'data' => $message
        ], 201);
    }

    public function show(Chat $message)
    {
        return $message;
    }
}
