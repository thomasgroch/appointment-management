<?php
namespace App\Http\Controllers;

use App\Models\Appointment;

class AppointmentController extends CrudController
{
    public function __construct()
    {
        $this->setModel(Appointment::class);
    }
}
