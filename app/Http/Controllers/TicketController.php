<?php

namespace App\Http\Controllers;

use App\Events\TicketCategoryChange;
use App\Events\TicketCreated;
use App\Events\TicketNewActualization;
use App\Events\TicketPriorityChanged;
use App\Events\TicketStatusChanged;
use App\Models\Ticket;
use App\Http\Controllers\Controller;
use App\Models\Actualization;
use App\Models\Department;
use App\Models\Status;
use App\Models\TicketActionHistory;
use App\Models\TicketCategory;
use App\Models\TicketAssignment;
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
        $tickets_abiertos = Ticket::with('user', 'department', 'status', 'ticket_category')->whereHas('status', function ($query) {
            $query->where('description', 'Abierto');
        })->get();
        foreach ($tickets_abiertos as $ticket_abierto) {
            $ticket_abierto->assignments = TicketAssignment::with('user')->where('ticket_id', $ticket_abierto->id)->get();
        }

        $tickets_en_proceso = Ticket::with('user', 'department', 'status', 'ticket_category')->whereHas('status', function ($query) {
            $query->where('description', 'En Proceso');
        })->get();
        foreach ($tickets_en_proceso as $ticket_en_proceso) {
            $ticket_en_proceso->assignments = TicketAssignment::with('user')->where('ticket_id', $ticket_en_proceso->id)->get();
        }

        $tickets_cancelados = Ticket::with('user', 'department', 'status', 'ticket_category')->whereHas('status', function ($query) {
            $query->where('description', 'Cancelado');
        })->get();

        foreach ($tickets_cancelados as $ticket_cancelado) {
            $ticket_cancelado->assignments = TicketAssignment::with('user')->where('ticket_id', $ticket_cancelado->id)->get();
        }
        $tickets_terminados = Ticket::with('user', 'department', 'status', 'ticket_category')->whereHas('status', function ($query) {
            $query->where('description', 'Terminado');
        })->get();
        foreach ($tickets_terminados as $ticket_terminado) {
            $ticket_terminado->assignments = TicketAssignment::with('user')->where('ticket_id', $ticket_terminado->id)->get();
        }
        $tickets = [...$tickets_abiertos, ...$tickets_en_proceso, ...$tickets_cancelados];
        $tickets_numbers = ['abiertos' => $tickets_abiertos->count(), 'en_proceso' => $tickets_en_proceso->count(), 'cancelados' => $tickets_cancelados->count(), 'terminados' => $tickets_terminados->count()];
        return response()->json(['status' => true, 'data' => ['tickets' => $tickets, 'numbers' => $tickets_numbers]]);
    }

    public function get_open_tickets()
    {
        $tickets_abiertos = Ticket::with([
            'user' => function ($query) {
                $query->select('id', 'names', 'surnames', 'color');
            },
            'department' => function ($query) {
                $query->select('id', 'description');
            },
            'status' => function ($query) {
                $query->select('id', 'description');
            },
            'ticket_category' => function ($query) {
                $query->select('id', 'description', 'color');
            },
        ])->whereHas('status', function ($query) {
            $query->where('description', 'Abierto');
        })->paginate();
        foreach ($tickets_abiertos as $ticket_abierto) {
            $ticket_abierto->assignments = TicketAssignment::with(['user' => function ($query) {
                $query->select('id', 'names', 'surnames', 'color');
            }])->where('ticket_id', $ticket_abierto->id)->get();
        }
        return response()->json(['status' => true, 'data' => $tickets_abiertos]);
    }

    public function get_in_process_tickets()
    {
        $tickets_en_proceso = Ticket::with([
            'user' => function ($query) {
                $query->select('id', 'names', 'surnames', 'color');
            },
            'department' => function ($query) {
                $query->select('id', 'description');
            },
            'status' => function ($query) {
                $query->select('id', 'description');
            },
            'ticket_category' => function ($query) {
                $query->select('id', 'description', 'color');
            },
        ])->whereHas('status', function ($query) {
            $query->where('description', 'En Proceso');
        })->paginate();
        foreach ($tickets_en_proceso as $ticket_en_proceso) {
            $ticket_en_proceso->assignments = TicketAssignment::with(['user' => function ($query) {
                $query->select('id', 'names', 'surnames', 'color');
            }])->where('ticket_id', $ticket_en_proceso->id)->get();
        }
        return response()->json(['status' => true, 'data' => $tickets_en_proceso]);
    }

    public function get_cancelled_tickets()
    {
        $tickets_cancelados = Ticket::with([
            'user' => function ($query) {
                $query->select('id', 'names', 'surnames', 'color');
            },
            'department' => function ($query) {
                $query->select('id', 'description');
            },
            'status' => function ($query) {
                $query->select('id', 'description');
            },
            'ticket_category' => function ($query) {
                $query->select('id', 'description', 'color');
            },
        ])->whereHas('status', function ($query) {
            $query->where('description', 'Cancelado');
        })->paginate();
        foreach ($tickets_cancelados as $ticket_cancelado) {
            $ticket_cancelado->assignments = TicketAssignment::with(['user' => function ($query) {
                $query->select('id', 'names', 'surnames', 'color');
            }])->where('ticket_id', $ticket_cancelado->id)->get();
        }
        return response()->json(['status' => true, 'data' => $tickets_cancelados]);
    }

    public function get_finished_tickets()
    {
        $tickets_terminados = Ticket::with([
            'user' => function ($query) {
                $query->select('id', 'names', 'surnames', 'color');
            },
            'department' => function ($query) {
                $query->select('id', 'description');
            },
            'status' => function ($query) {
                $query->select('id', 'description');
            },
            'ticket_category' => function ($query) {
                $query->select('id', 'description', 'color');
            },
        ])->whereHas('status', function ($query) {
            $query->where('description', 'Terminado');
        })->paginate();
        foreach ($tickets_terminados as $ticket_terminado) {
            $ticket_terminado->assignments = TicketAssignment::with(['user' => function ($query) {
                $query->select('id', 'names', 'surnames', 'color');
            }])->where('ticket_id', $ticket_terminado->id)->get();
        }
        return response()->json(['status' => true, 'data' => $tickets_terminados]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    public function get_ticket_actualizations(Ticket $ticket, Request $request)
    {
        $actualizations = Actualization::with(['user' => function ($query) {
            $query->select('id', 'names', 'surnames', 'color');
        }])->where('ticket_id', $ticket->id)->get();
        return response()->json(['status' => true, 'data' => $actualizations]);
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
            $actualizations = Actualization::where('ticket_id', $ticket->id)->with('user')->get();
            broadcast(new TicketNewActualization($user->department_id));
            return response()->json(['status' => true, 'data' => $actualizations]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'data' => $th->getMessage()], 200);
        }
    }
    public function get_ticket_by_id(Ticket $ticket)
    {
        $ticket_res = Ticket::with('department', 'user', 'status', 'ticket_category')->where('id', $ticket->id)->first();
        $ticket_res->new = 0;
        $ticket_res->save();
        $actualizations = Actualization::with(['user' => function ($query) {
            $query->select('id', 'names', 'surnames', 'color');
        }])->where('ticket_id', $ticket->id)->get();

        return response()->json(['status' => true, 'data' => $ticket_res, 'actualizations' => $actualizations]);
    }

    public function change_ticket_status(Ticket $ticket, Request $request)
    {
        $user = $request->user();
        $department = Department::where('description', 'IT')->firstOrNew();
        $ticket_res = Ticket::with('department', 'status', 'user')->where('id', $ticket->id)->first();
        $prev_status = $ticket_res->status->description;
        $status = Status::where('description', $request->status)->first();
        $ticket_res->new = 1;
        $ticket_res->status()->associate($status->id);
        $ticket_res->save();
        // Historial de accion de tickets
        $ticket_action = TicketActionHistory::create(['description' => "El usuario $user->names $user->surnames ($user->document) cambio el status del ticket (Nuevo Status: $request->status, Previo Status: $prev_status)."]);
        $ticket_action->user()->associate($user->id);
        $ticket_action->ticket()->associate($ticket_res->id);
        $ticket_action->save();
        $new_ticket = Ticket::with('department', 'user', 'status', 'ticket_category')->where('id', $ticket->id)->first();
        $assignments = TicketAssignment::with(['user' => function ($query) {
            $query->select('id', 'names', 'surnames', 'color');
        }])->where('ticket_id', $new_ticket->id)->get();
        $new_ticket->assignments = $assignments;
        broadcast(new TicketStatusChanged($new_ticket, $prev_status, $department->id));
        return response()->json(['status' => true, 'data' => $new_ticket]);
    }

    public function change_ticket_priority(Ticket $ticket, Request $request)
    {

        $user = $request->user();
        $department = Department::where('description', 'IT')->firstOrNew();
        $ticket_res = Ticket::with('department', 'status', 'user')->where('id', $ticket->id)->first();
        $ticket_res->priority = $request->priority;
        $ticket_res->save();
        // Historial de accion de tickets
        $ticket_action = TicketActionHistory::create(['description' => "El usuario $user->names $user->surnames ($user->document) cambio la prioridad del ticket.",]);
        $ticket_action->user()->associate($user->id);
        $ticket_action->ticket()->associate($ticket_res->id);
        $ticket_action->save();
        $new_ticket = Ticket::with('department', 'user', 'status', 'ticket_category')->where('id', $ticket->id)->first();
        broadcast(new TicketPriorityChanged($request->priority, $new_ticket, $department->id));
        return response()->json(['status' => true, 'data' => $new_ticket]);
    }
    public function change_ticket_category(Ticket $ticket, Request $request)
    {

        $department = Department::where('description', 'IT')->firstOrNew();
        $user = $request->user();
        try {
            $ticket_cat = TicketCategory::where(['description' => $request->description])->firstOrFail();
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'error' => ['No se logro crear la categoria', $th->getMessage()]], 400);
        }

        $ticket_res = Ticket::with('department', 'status', 'user', 'ticket_category')->where('id', $ticket->id)->first();
        $ticket_res->ticket_category()->associate($ticket_cat->id);
        $ticket_res->save();
        // Historial de accion de tickets
        $ticket_action = TicketActionHistory::create(['description' => "El usuario $user->names $user->surnames ($user->document) cambio la categoria del ticket.",]);
        $ticket_action->user()->associate($user->id);
        $ticket_action->ticket()->associate($ticket_res->id);
        $ticket_action->save();
        broadcast(new TicketCategoryChange($ticket_cat, $department->id));
        $new_ticket = Ticket::with('department', 'user', 'status', 'ticket_category')->where('id', $ticket->id)->first();
        return response()->json(['status' => true, 'data' => $new_ticket]);
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
            'priority' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 400);
        }
        try {
            $ticket_user = User::select('id', 'names', 'surnames', 'department_id')->with('department:id,description')->where('id', $request->user_id)->firstOrFail();
            $ticket = Ticket::create([
                'description' => $request->description,
                'priority' => $request->priority ? $request->priority : 'Sin prioridad',
                'number_of_actualizations' => 0,
            ]);
            $status_abierto = Status::firstOrCreate(['description' => 'Abierto']);
            $ticket_category = TicketCategory::firstOrCreate(['description' => 'Sin Categoria', 'color' => '#525252']);
            $ticket->ticket_category()->associate($ticket_category->id);
            $ticket->user()->associate($ticket_user->id);
            $ticket->department()->associate($ticket_user->department->id);
            $ticket->status()->associate($status_abierto->id);
            $ticket->save();
            // Historial de accion de tickets
            // $ticket_action = TicketActionHistory::create(['description' => "El usuario $ticket_user->names $ticket_user->surnames ($ticket_user->document) creo un ticket nuevo (id: $ticket->id, description: $ticket->description)",]);
            // $ticket_action->user()->associate($ticket_user->id);
            // $ticket_action->ticket()->associate($ticket->id);
            // $ticket_action->save();
            $it_department = Department::where('description', 'IT')->firstOrFail();
            $ticketFullData = Ticket::with('department', 'user', 'status', 'ticket_category')->where('id', $ticket->id)->first();
            broadcast(new TicketCreated($ticketFullData, $it_department->id));
            return response()->json(['status' => true, 'data' => [$ticket]]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['status' => false, 'errors' => ['No se logro crear el ticket', $th->getMessage()]], 500);
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
