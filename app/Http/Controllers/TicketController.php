<?php

namespace App\Http\Controllers;

use App\Events\TicketUpdated;
use App\Models\Ticket;
use App\Http\Controllers\Controller;
use App\Models\Actualization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $tickets_abiertos = Ticket::where('status', 'Abierto')->with('user', 'department')->get();
        $tickets_en_proceso = Ticket::where('status', 'En Proceso')->with('user', 'department')->get();
        $tickets_cancelados = Ticket::where('status', 'Cancelado')->with('user', 'department')->get();
        $tickets_terminados = Ticket::where('status', 'Terminado')->with('user', 'department')->get();
        $tickets = [...$tickets_abiertos, ...$tickets_en_proceso, ...$tickets_cancelados];
        $tickets_numbers = ['abiertos' => $tickets_abiertos->count(), 'en_proceso' => $tickets_en_proceso->count(), 'cancelados' => $tickets_cancelados->count(), 'terminados' => $tickets_terminados->count()];
        return response()->json(['status' => true, 'data' => ['tickets' => $tickets, 'numbers' => $tickets_numbers]]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    public function new_ticket_actualization(Ticket $ticket, Request $request)
    {
        //
        $user_req = $request->user();
        $user = User::where('id', $user_req->id)->first();
        try {
            $actualization = Actualization::create([
                'description' => $request->description,
            ]);
            $actualization->ticket()->associate($ticket);
            $actualization->user()->associate($user);
            $actualization->save();
            $actualizations = Actualization::with('user', 'ticket')->where('ticket_id', $ticket->id)->get();
            return response()->json(['status' => true, 'data' => $actualizations]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'data' => $th->getMessage()], 200);
        }
    }
    public function get_ticket_by_id(Ticket $ticket)
    {
        $ticket_res = Ticket::with('department', 'user')->where('id', $ticket->id)->first();
        $actualizations = Actualization::with('user')->where('ticket_id', $ticket->id)->get();

        return response()->json(['status' => true, 'data' => $ticket_res, 'actualizations' => $actualizations]);
    }
    public function ticket_move(Request $request, Ticket $ticket)
    {
        try {

            $status = $request->status;
            $ticket->status = $status;
            $ticket->save();
            return response()->json(['status' => true, 'data' => [$request->status]]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => [$e->getMessage()]], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'description' => 'required|string',
            'category' => 'required|exists:ticket_categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 400);
        }
        try {
            $ticket_user = User::select('names', 'surnames', 'department_id')->with('department:id,description')->where('id', $request->user_id)->firstOrFail();
            $ticket = Ticket::create([
                'description' => $request->description,
                'priority' => 'Sin prioridad',
                'number_of_actualizations' => 0,
            ]);
            $ticket->user()->associate($ticket_user->id);
            $ticket->department()->associate($ticket_user->department->id);
            return response()->json(['status' => true, 'data' => [$ticket_user]]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['status' => false, 'errors' => ['No se logro crear el ticket', $th->getMessage()], 500]);
        }
    }

    /**
     * Display the specified resource.
     */
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
