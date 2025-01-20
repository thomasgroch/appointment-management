<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Phone;
use App\Models\Appointment;

class Patient extends Authenticatable
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
        'cpf',
        'cep',
        'address',
        'number'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:patients,email',
        'password' => 'required|min:8|confirmed',
        'cpf' => 'required|string|size:11',
        'cep' => 'required|string|size:8',
        'address' => 'required|string|max:255',
        'number' => 'required|integer|min:1',
    ];
    public static $updateRules = [
        'name' => 'sometimes|string|max:255',
        'email' => 'sometimes|email|unique:patients,email',
        'password' => 'sometimes|min:8|confirmed',
        'cpf' => 'sometimes|string|size:11',
        'cep' => 'sometimes|string|size:8',
        'address' => 'sometimes|string|max:255',
        'number' => 'sometimes|integer|min:1',
    ];

    public function phones()
    {
        return $this->hasMany(Phone::class);
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
