<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{
    protected $loginRules = [
        'email' => ['required', 'email'],
        'password' => ['required'],
    ];

    public function me(Request $request)
    {
        return $this->respond($request->user());
    }

    public function loginDoctor(Request $request)
    {
        $credentials = $request->validate($this->loginRules);

        $user = Doctor::where('email', $request->email)->first();

        if (!$user || !Auth::guard('doctor')->attempt($credentials)) {
            return $this->respondWithError('Invalid credentials', 401);
        }

        return $this->respond([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $user->createToken('auth_token')->plainTextToken
        ]);
    }

    public function loginPatient(Request $request)
    {
        $credentials = $request->validate($this->loginRules);

        $user = Patient::where('email', $request->email)->first();

        if (!$user || !Auth::guard('patient')->attempt($credentials)) {
            return $this->respondWithError('Invalid credentials', 401);
        }

        return $this->respond([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $user->createToken('auth_token')->plainTextToken
        ]);
    }

    protected function createToken(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'abilities' => 'nullable|array'
        ]);

        $user = $request->user();
        $token = $user->createToken($request->name, $request->abilities ?? ['*']);

        return response()->json([
            'data' => [
                'token' => $token->plainTextToken,
                'name' => $request->name
            ]
        ], 201);
    }

    protected function registerDoctor(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:doctors,email',
            'password' => 'required|string|min:8|confirmed',
            'crm' => 'required|string|unique:doctors,crm',
            'specialty_id' => 'required|exists:specialties,id'
        ]);

        $doctor = Doctor::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'crm' => $validated['crm'],
            'specialty_id' => $validated['specialty_id']
        ]);

        $token = $doctor->createToken('auth_token')->plainTextToken;

        return $this->respond([
            'user' => $doctor,
            'token' => $token
        ], 201);
    }

    protected function registerPatient(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:patients,email',
            'password' => 'required|string|min:8|confirmed',
            'cpf' => 'required|string|unique:patients,cpf',
            'cep' => 'required|string',
            'address' => 'required|string',
            'number' => 'required|string'
        ]);

        $patient = Patient::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'cpf' => $validated['cpf'],
            'cep' => $validated['cep'],
            'address' => $validated['address'],
            'number' => $validated['number']
        ]);

        $token = $patient->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data' => [
                'user' => $patient,
                'token' => $token
            ]
        ], 201);
    }

    protected function emailForgetPassword(Request $request, string $model)
    {
        $request->validate(['email' => 'required|email']);

        $user = $model::where('email', $request->email)->first();

        if (!$user) {
            return $this->respondWithError('User not found', 404);
        }

        return $this->respond(['message' => 'Password reset email sent successfully']);
    }

    protected function resetPassword(Request $request, string $model)
    {
        $request->validate($this->passwordResetRules);

        $user = $model::where('email', $request->email)->first();

        if (!$user) {
            return $this->respondWithError('User not found', 404);
        }

        $user->update(['password' => $request->password]);

        return $this->respond(['message' => 'Password updated successfully']);
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        $request->session()->invalidate();

        return redirect('/')
            ->with(['message' => 'Logged out successfully'])
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
