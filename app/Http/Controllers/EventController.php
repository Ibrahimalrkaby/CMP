<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
     // Create an event
     public function store(Request $request)
     {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $request->validate([
             'title' => 'required|string|max:255',
             'description' => 'required|string',
             'event_date' => 'required|date',
             'location' => 'required|string|max:255',
        ]);
 
        $event = Event::create($request->all());
 
         return response()->json([
             'message' => 'Event created successfully',
             'data' => $event
        ]);
    }
 
    // List all events
    public function index()
    {
        $events = Event::orderBy('event_date', 'asc')->get();
        return response()->json($events);
    }
 
     // Show specific event
    public function show($id)
    {
        $event = Event::findOrFail($id);
        return response()->json($event);
    }
 
    // Delete an event
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();
 
        return response()->json(['message' => 'Event deleted successfully']);
    }
}
