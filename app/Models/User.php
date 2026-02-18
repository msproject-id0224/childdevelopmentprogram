<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_MENTOR = 'mentor';
    public const ROLE_PARTICIPANT = 'participant';
    public const MINIMUM_PARTICIPANT_AGE = 13;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'nickname',
        'id_number',
        'email',
        'role',
        'is_active',
        'date_of_birth',
        'age',
        'gender',
        'education',
        'age_group',
        'height',
        'weight',
        'communication',
        'phone_number',
        'address',
        'specialization',
        'experience',
        'bio',
        'mentor_id',
        'profile_photo_path',
        'profile_photo_status',
        'job_title',
    ];

    /**
     * Set the user's email (lowercase and trim).
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower(trim($value));
    }

    /**
     * Get the user's age based on date of birth, or return stored age.
     */
    public function getAgeAttribute($value): ?int
    {
        return $this->date_of_birth ? (int) $this->date_of_birth->age : $value;
    }

    /**
     * Get the full name of the user.
     */
    public function getNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get the first name for display (handles titles and multi-word names).
     */
    public function getFirstNameDisplayAttribute(): string
    {
        $nameToProcess = $this->first_name ?: ($this->last_name ?: '');
        
        if (empty(trim($nameToProcess))) {
            return 'User';
        }

        $parts = explode(' ', trim($nameToProcess));
        $titles = ['Dr.', 'Ir.', 'Prof.', 'H.', 'Hj.', 'Mr.', 'Mrs.', 'Ms.'];
        
        // Check if first part is a title
        if (count($parts) > 1 && in_array($parts[0], $titles)) {
            // Check if second part is also a title (e.g. Prof. Dr.)
            if (count($parts) > 2 && in_array($parts[1], $titles)) {
                return $parts[2];
            }
            return $parts[1];
        }

        return $parts[0];
    }
    
    /**
     * Append the name attribute to the model's array form.
     */
    protected $appends = ['name', 'first_name_display', 'profile_photo_url'];

    /**
     * Get the profile photo URL.
     */
    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->profile_photo_path 
            ? asset('storage/' . $this->profile_photo_path) 
            : null;
    }

    public function rmdProfile(): HasOne
    {
        return $this->hasOne(RmdProfile::class);
    }

    public function rmdBibleReflection(): HasOne
    {
        return $this->hasOne(RmdBibleReflection::class);
    }

    public function rmdTrueSuccess(): HasOne
    {
        return $this->hasOne(RmdTrueSuccess::class);
    }

    public function rmdTheOnlyOne(): HasOne
    {
        return $this->hasOne(RmdTheOnlyOne::class);
    }

    public function rmdMultipleIntelligence(): HasOne
    {
        return $this->hasOne(RmdMultipleIntelligence::class);
    }

    public function rmdSocioEmotional(): HasOne
    {
        return $this->hasOne(RmdSocioEmotional::class);
    }

    public function rmdCareerExploration(): HasOne
    {
        return $this->hasOne(RmdCareerExploration::class);
    }

    public function rmdCareerExplorationP2(): HasOne
    {
        return $this->hasOne(RmdCareerExplorationP2::class);
    }

    public function rmdPreparationDreamIsland(): HasOne
    {
        return $this->hasOne(RmdPreparationDreamIsland::class);
    }

    public function profilePhotoRequests()
    {
        return $this->hasMany(ProfilePhotoRequest::class);
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function assignedParticipants()
    {
        return $this->hasMany(User::class, 'mentor_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isMentor(): bool
    {
        return $this->role === self::ROLE_MENTOR;
    }

    public function isParticipant(): bool
    {
        return $this->role === self::ROLE_PARTICIPANT;
    }

    /**
     * Check if the user meets the minimum age requirement.
     * Only applies to participants.
     */
    public function meetsAgeRequirement(): bool
    {
        if (!$this->isParticipant()) {
            return true;
        }

        return $this->age >= self::MINIMUM_PARTICIPANT_AGE;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'date_of_birth' => 'date',
            'height' => 'decimal:2',
            'weight' => 'decimal:2',
        ];
    }
}
