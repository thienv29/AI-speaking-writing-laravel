<?php

namespace App\Http\Controllers;

use App\Models\ExerciseType;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ExerciseTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(ExerciseType::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'code' => ['required', 'string', 'max:100', 'alpha_dash', 'unique:exercise_types,code'],
            ],
            [
                'name.required' => 'The exercise type name is required.',
                'name.string' => 'The exercise type name must be a string.',
                'name.max' => 'The exercise type name may not be greater than 255 characters.',
                'code.required' => 'The exercise type code is required.',
                'code.string'=> 'The exercise type code must be a string.',
                'code.max' => 'The exercise type code may not be greater than 100 characters.',
                'code.alpha_dash' => 'The exercise type code may only contain letters, numbers, dashes, and underscores.',
                'code.unique' => 'The exercise type code has already been taken.'
            ]);

            $validated['name'] = trim($validated['name']);
            $validated['code'] = strtoupper(trim($validated['code']));

            $exerciseType = ExerciseType::create($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Exercise type created successfully.',
                'data'   => $exerciseType
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'Validation error.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            Log::error('ExerciseType store error', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot create exercise type: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ExerciseType  $exerciseType
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function show(ExerciseType $exerciseType)
    {
        try {
            return response()->json([
                'status' => 'success',
                'data'   => $exerciseType,
            ]);
        } catch (\Throwable $e) {
            Log::error('ExerciseType show error', ['id' => $exerciseType->id ?? null, 'error' => $e]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot fetch exercise type: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ExerciseType  $exerciseType
     * @return \Illuminate\Http\Response
     */
    public function edit(ExerciseType $exerciseType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ExerciseType  $exerciseType
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, ExerciseType $exerciseType)
    {
        try {
            $input = $request->all();
            if ($request->has('name')) {
                $input['name'] = trim((string) $request->input('name'));
            }
            if ($request->has('code')) {
                $input['code'] = strtoupper(trim((string) $request->input('code')));
            }

            $validated = validator(
                $input,
                [
                    'name' => ['sometimes', 'required', 'string', 'max:255'],
                    'code' => [
                        'sometimes','required','string','max:100','alpha_dash',
                        Rule::unique('exercise_types','code')->ignore($exerciseType->id),
                    ],
                ],
                [
                    'name.required'     => 'The exercise type name is required.',
                    'name.string'       => 'The exercise type name must be a string.',
                    'name.max'          => 'The exercise type name may not be greater than 255 characters.',
                    'code.required'     => 'The exercise type code is required.',
                    'code.string'       => 'The exercise type code must be a string.',
                    'code.max'          => 'The exercise type code may not be greater than 100 characters.',
                    'code.alpha_dash'   => 'The exercise type code may only contain letters, numbers, dashes, and underscores.',
                    'code.unique'       => 'The exercise type code has already been taken.',
                ]
            )->validate();

            if (empty($validated)) {
                return response()->json([
                    'status'  => 'fail',
                    'message' => 'No fields to update.',
                ], 422);
            }

            $exerciseType->fill($validated)->save();
            $exerciseType->loadCount('exercises');

            return response()->json([
                'status' => 'success',
                'data'   => $exerciseType,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'Validation error.',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Throwable $e) {
            Log::error('ExerciseType update error', ['id' => $exerciseType->id ?? null, 'error' => $e]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot update exercise type: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ExerciseType  $exerciseType
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function destroy(ExerciseType $exerciseType)
    {
        try {
            if ($exerciseType->exercises()->exists()) {
                return response()->json([
                    'status'  => 'fail',
                    'message' => 'Cannot delete: there are exercises referencing this type.',
                ], 409);
            }

            $exerciseType->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Exercise type deleted.',
            ]);
        } catch (\Throwable $e) {
            Log::error('ExerciseType destroy error', ['id' => $exerciseType->id ?? null, 'error' => $e]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot delete exercise type: ' . $e->getMessage(),
            ], 500);
        }
    }
}
