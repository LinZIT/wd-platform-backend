<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketAssignment;
use App\Http\Controllers\Controller;
use App\Models\Actualization;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function get_all_assignments(Ticket $ticket)
    {
        try {
            //code...
            $ticket_assignments = TicketAssignment::with('user')->where('ticket_id', $ticket->id)->get();
            return response()->json(['status' => true, 'data' => $ticket_assignment]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['status' => false, 'errors' => ['El ticket no tiene asignaciones', $th->getMessage()], 400]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    /**
     * Display the specified resource.
     */
    public function ticket_assignment(Ticket $ticket, User $user)
    {
        //
        try {
            //code...
            $ticket_assignment = TicketAssignment::create();
            $ticket_assignment->user()->associate($user);
            $ticket_assignment->ticket()->associate($ticket);
            $ticket_assignment->save();

            $ticket_assignments = TicketAssignment::with('user')->where('ticket_id', $ticket->id)->get();
            return response()->json(['status' => true, 'data' => $ticket_assignment]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['status' => false, 'errors' => ['No se logro asignar el ticket', $th->getMessage()], 400]);
        }
    }
    public function show(Ticket $ticket)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        //
    }
}
