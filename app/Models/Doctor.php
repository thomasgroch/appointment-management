<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Specialty;
use App\Models\Appointment;

class Doctor extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'crm',
        'specialty_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static $updateRules = [
        'name' => 'sometimes|string|max:255',
        'email' => 'sometimes|email|unique:doctors,email',
        'password' => 'sometimes|min:8',
        'crm' => 'sometimes|unique:doctors,crm',
        'specialty_id' => 'sometimes|exists:specialties,id',
    ];

    public static $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:doctors,email',
        'password' => 'required|min:8',
        'crm' => 'required|unique:doctors,crm',
        'specialty_id' => 'required|exists:specialties,id',
    ];


    /**
     * Check if doctor is a specialist in a specific specialty.
     *
     * @return bool
     */
    public function isSpecialistIn($specialtyId)
    {
        return $this->specialty_id === $specialtyId;
    }

    /**
     * Relationship with specialty.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    /**
     * Relationship with appointments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
