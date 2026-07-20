<?php

namespace App\Http\Controllers\Api;

use App\Models\TicketType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TicketTypeController extends Controller
{
    
    public function store(Request $request) 
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quota' => 'required|integer',
        ]);

        $ticketType = TicketType::create([
            'event_id' => $validated['event_id'],
            'name' => $validated['name'],
            'price' => $validated['price'],
            'quota' => $validated['quota'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dibuat',
            'data' => $ticketType
        ], 201);
    }
}
