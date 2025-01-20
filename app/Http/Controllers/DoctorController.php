<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends CrudController
{
    public function __construct()
    {
        $this->setModel(Doctor::class);
    }
}
