<?php
namespace App\Http\Controllers;

use App\Models\Patient;

class PatientController extends CrudController
{
    public function __construct()
    {
        $this->setModel(Patient::class);
    }
}
