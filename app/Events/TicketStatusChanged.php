<?php

namespace App\Events;

use App\Models\Ticket;
use App\Models\TicketAssignment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Ticket $ticket, public string $prev_status, public int $department_id)
    {
        //
    }
    public function broadcastWith()
    {
        $new_ticket = Ticket::with([
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
        ])->where('id', $this->ticket->id)->first();
        $assignments = TicketAssignment::with(['user' => function ($query) {
            $query->select('id', 'names', 'surnames', 'color');
        }])->where('ticket_id', $new_ticket->id)->get();
        $new_ticket->assignments = $assignments;
        return [
            'ticket' => $new_ticket,
            'prev_status' => $this->prev_status,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('ticketsRoom.' . $this->department_id),
        ];
    }
}
