<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with(['user', 'ticketTypes']);

        if ($request->has('search')) {
                    $query->where('title', 'like', '%' . $request->search . '%')
                        ->orWhere('location', 'like', '%' . $request->search . '%')
                        ->orWhere('event_date', 'like', '%' . $request->search . '%')
                        ->orWhere('description', 'like', '%' . $request->search . '%');
                }
        $events = $query->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diambil',
            'data' => $events
        ], 200);        

    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'event_date' => 'required|date',
            'description' => 'required|string|max:255',
        ]);

        $event = Event::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'location' => $validated['location'],
            'event_date' => $validated['event_date'],
            'description' => $validated['description'],
        ]);

        return response()->json([
            'success' => true,
            'message'=> 'Event berhasil dibuat',
            'data' => $event
        ], 201);

    }

    public function show(Event $event) 
    {
        $event->load('user', 'ticketTypes');

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil ditampilkan',
            'data' => $event
        ], 200);
    }
}
