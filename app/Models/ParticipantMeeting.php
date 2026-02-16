<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParticipantMeeting extends Model
{
    protected $fillable = [
        'participant_id',
        'mentor_id',
        'scheduled_at',
        'location',
        'agenda',
        'status',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function participant()
    {
        return $this->belongsTo(User::class, 'participant_id');
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }
}
