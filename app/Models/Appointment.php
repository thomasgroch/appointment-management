<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    /**
    * The validation rules for creating a new appointment
    * @var array
    */
    public static $rules = [
        'patient_id' => 'required|exists:patients,id',
        'doctor_id' => 'required|exists:doctors,id',
        'appointment_time' => 'required|date_format:Y-m-d H:i:s'
    ];

    /**
    * The validation rules for updating an appointment
    * @var array
    */
    public static $updateRules = [
        'patient_id' => 'sometimes|exists:patients,id',
        'doctor_id' => 'sometimes|exists:doctors,id',
        'appointment_time' => 'sometimes|date_format:Y-m-d H:i:s'
    ];

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_time',
    ];
}
