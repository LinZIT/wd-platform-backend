<?php

namespace App\Events;

use App\Models\TicketAssignment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketAssignUser implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public int $ticket_id, public int $assigned_user, public string $action, public int $department_id)
    {
        //
    }
    public function broadcastWith()
    {
        $ticket_assignments = TicketAssignment::with(['user' => function ($query) {
            $query->select('id', 'names', 'surnames', 'color');
        }])->where('ticket_id', $this->ticket_id)->get();
        return [
            'ticket_assignments' => $ticket_assignments,
            'assigned_user' => $this->assigned_user,
            'action' => $this->action,
            'ticket_id' => $this->ticket_id,
            'department_id' => $this->department_id,
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
