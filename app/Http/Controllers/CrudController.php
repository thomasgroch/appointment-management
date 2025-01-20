<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

abstract class CrudController extends BaseController
{
    private string $modelClass;
    protected array $with = [];
    protected int $perPage = 15;

    /**
     * Get the model class name
     *
     * @return string The fully qualified model class name
     */
    protected function getModelClass(): string
    {
        return $this->modelClass;
    }

    /**
    * Set the model class name
    *
    * @param string $modelClass The fully qualified model class name
    */
    protected function setModel(string $modelClass): void
    {
        $this->modelClass = $modelClass;
    }

    protected function rules(int $id = null): array
    {
        $modelClass = $this->modelClass;
        if ($id !== null && isset($modelClass::$updateRules)) {
            return $modelClass::$updateRules;
        }
        if (isset($modelClass::$rules)) {
            return $modelClass::$rules;
        }
        return [];
    }

    protected function formatResponse($data, string $message = '', int $code = 200): array
    {
        return [
            'data' => $data,
            'message' => $message,
            'code' => $code
        ];
    }

    public function index(Request $request)
    {
        $query = $this->getModelClass()::query()->with($this->with);

        if ($request->has('per_page')) {
            $data = $query->paginate($request->get('per_page', $this->perPage));
        } else {
            $data = $query->get();
        }

        return $this->respond($data);
    }

    public function store(Request $request)
    {
        $rules = $this->rules();
        if (!empty($rules)) {
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The given data was invalid',
                    'errors' => $validator->errors()->toArray()
                ], 422);
            }
        }

        $data = $request->all();

        try {
            DB::beginTransaction();
            $resource = $this->getModelClass()::create($data);
            DB::commit();
            $resource->load($this->with);

            return $this->respond($resource, 'Resource created successfully', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create resource', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->respondWithError('An unexpected error occurred while creating the resource', 500);
        }
    }

    public function show($id)
    {
        $resource = $this->getModelClass()::query()->with($this->with)->find($id);

        if (!$resource) {
            return $this->respondWithError('Resource not found', 404);
        }

        return $this->respond($resource);
    }

    public function update(Request $request, $id)
    {
        if (!empty($this->rules($id))) {
            $validator = Validator::make($request->all(), $this->rules($id));
            if ($validator->fails()) {
                return $this->respondWithError($validator->errors(), 422);
            }
        }
        $resource = $this->getModelClass()::query()->with($this->with)->find($id);

        if (!$resource) {
            return $this->respondWithError('Resource not found', 404);
        }

        $data = $request->all();

        try {
            DB::beginTransaction();
            $resource->update($data);
            DB::commit();

            return $this->respond($resource, 'Resource updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->respondWithError('Failed to update resource: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        $resource = $this->getModelClass()::query()->with($this->with)->find($id);

        if (!$resource) {
            return $this->respondWithError('Resource not found', 404);
        }

        try {
            DB::beginTransaction();
            $resource->delete();
            DB::commit();

            return $this->respond(null, 'Resource deleted successfully', 204);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->respondWithError('Failed to delete resource: ' . $e->getMessage(), 500);
        }
    }
}
