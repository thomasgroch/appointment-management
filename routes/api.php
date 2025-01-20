<?php
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AuthController;

Route::post('token', [AuthController::class, 'createToken']);
Route::get('logout', [AuthController::class, 'logout']);
Route::get('password/email', [AuthController::class, 'resetPassword']);

Route::prefix('doctor')->group(function () {
    Route::post('register', [AuthController::class, 'registerDoctor']);
    Route::post('login', [AuthController::class, 'loginDoctor']);
    Route::post('forgot-password', [AuthController::class, 'forgotPasswordDoctor']);
});
Route::prefix('patient')->group(function () {
    Route::post('register', [AuthController::class, 'registerPatient']);
    Route::post('login', [AuthController::class, 'loginPatient']);
    Route::post('forgot-password', [AuthController::class, 'forgotPasswordPatient']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('doctors', DoctorController::class);
    Route::resource('patients', PatientController::class);
    Route::resource('appointments', AppointmentController::class);
    Route::get('me', [AuthController::class, 'me']);
});

