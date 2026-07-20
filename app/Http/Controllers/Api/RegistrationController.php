<?php

namespace App\Http\Controllers\Api;

use App\Models\Registration;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TicketType;

class RegistrationController extends Controller
{
    public function store(Request $request) 
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'ticket_type_id' => 'required|exists:ticket_types,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $ticketType = TicketType::find($validated['ticket_type_id']);

        if ($ticketType->event_id != $validated['event_id']) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket ini bukan untuk event yang dipilih'
            ], 422);
        }

        if ($ticketType->quota < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Kuota tidak mencukupi'
            ], 409);
        }

        $ticketType->decrement('quota', $validated['quantity']);

        $registration = Registration::create([
            'user_id' => $request->user()->id,
            'event_id' => $validated['event_id'],
            'ticket_type_id' => $validated['ticket_type_id'],
            'quantity' => $validated['quantity'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Success create registration',
            'data' => $registration
        ], 201);

    }
}
