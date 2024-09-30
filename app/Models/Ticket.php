<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    /**
     * Get the department that owns the user.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }


    public function ticket_category()
    {
        return $this->belongsTo(TicketCategory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticket_actualization()
    {
        return $this->belongsToMany(Actualization::class);
    }

    protected $fillable = [
        'description',
        'category',
        'priority',
        'number_of_actualizations',
    ];
}
