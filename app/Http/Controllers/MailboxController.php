<?php

namespace App\Http\Controllers;

use App\Models\Mailbox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MailboxController extends Controller
{
    // send a message to a student
    public function sendMessage(Request $request)
    {
        $user = Auth::user();
        if (!in_array(Auth::user()->role, ['admin', 'doctor'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $request->validate([
            'sender_id' => 'required|integer',
            'sender_type' => 'required|in:admin,doctor,assistant',
            'student_id' => 'required|exists:students_data,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $message = Mailbox::create($request->all());

        return response()->json(['message' => 'Message sent', 'data' => $message]);
    }

    // show all messages for a specific student
    public function studentInbox($student_id)
    {
        $messages = Mailbox::where('student_id', $student_id)->orderBy('created_at', 'desc')->get();
        return response()->json($messages);
    }

    // read a specific message and mark it as read
    public function readMessage($id)
    {
        $message = Mailbox::findOrFail($id);
        $message->is_read = true;
        $message->save();

        return response()->json(['message' => 'Message marked as read', 'data' => $message]);
    }

    // delete a specific message
    public function deleteMessage($id)
    {
        $message = Mailbox::findOrFail($id);
        $message->delete();

        return response()->json(['message' => 'Message deleted']);
    }
}
