<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $users = User::whereNull('deleted_at')->get();

        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate(
                [
                    'name' => ['required','string','max:255'],
                    'email' => ['required','string','email','max:255','unique:users,email'],
                    'password' => ['required', 'confirmed', 'string','min:8','max:255'], 
                    'dob' => ['nullable','date'],
                    'phone_number' => ['nullable','string','max:15', 'unique:users,phone_number'],
                    'avatar_url' => ['nullable','url','max:2048'],
                    'role' => ['nullable','string','in:user,admin'],
                ],
                [
                    'name.required'     => 'The name is required.',
                    'name.string'       => 'The name must be a string.',
                    'name.max'          => 'The name may not be greater than 255 characters.',

                    'email.required'    => 'The email is required.',
                    'email.string'      => 'The email must be a string.',
                    'email.email'       => 'The email is invalid.',
                    'email.max'         => 'The email may not be greater than 255 characters.',
                    'email.unique'      => 'The email has already been taken.',

                    'password.required' => 'The password is required.',
                    'password.confirmed' => 'The password confirmation does not match.',
                    'password.string'   => 'The password must be a string.',
                    'password.min'      => 'The password must be at least 8 characters.',
                    'password.max'      => 'The password may not be greater than 255 characters.',

                    'dob.date'          => 'The date of birth is not a valid date.',

                    'phone_number.string' => 'The phone number must be a string.',
                    'phone_number.max'    => 'The phone number may not be greater than 15 characters.',
                    'phone_number.unique' => 'The phone number has already been taken.',

                    'avatar_url.url'    => 'The avatar URL must be a valid URL.',
                    'avatar_url.max'    => 'The avatar URL may not be greater than 2048 characters.',

                    'role.in'          => 'The role must be either user or admin.',
                    'role.string'      => 'The role must be a string.',
                ]
            );

            $validated['name']  = trim($validated['name']);
            $validated['email'] = strtolower(trim($validated['email']));
            if (array_key_exists('avatar_url', $validated) && !is_null($validated['avatar_url'])) {
                $validated['avatar_url'] = trim($validated['avatar_url']);
            }
            if (array_key_exists('dob', $validated) && !is_null($validated['dob'])) {
                $validated['dob'] = trim($validated['dob']);
            }
            if (array_key_exists('phone_number', $validated) && !is_null($validated['phone_number'])) {
                $validated['phone_number'] = trim($validated['phone_number']);
            }
            if (array_key_exists('role', $validated) && !is_null($validated['role'])) {
                $validated['role'] = strtolower(trim($validated['role']));
            }
            $validated['password'] = Hash::make($validated['password']);

            $user = User::create($validated)->makeHidden(['password']);

            return response()->json([
                'status'  => 'success',
                'message' => 'User created successfully.',
                'data'    => $user,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status'=>'fail',
                'message'=>'Validation error.',
                'errors'=>$e->errors()
            ], 422);
        } catch (\Throwable $e) {
            Log::error('User store error', ['error'=>$e]);
            return response()->json([
                'status'=>'error',
                'message'=>'Cannot create user: '.$e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function show(User $user)
    {
        try {
            $user->load([
                'attempts',   
            ])->loadCount(['attempts']);

            return response()->json([
                'status' => 'success',
                'data'   => $user->makeHidden(['password']),
            ]);
        } catch (\Throwable $e) {
            Log::error('User show error', ['id'=>$user->id ?? null, 'error'=>$e]);
            return response()->json([
                'status'=>'error',
                'message'=>'Cannot fetch user: '.$e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user)
    {
        try {
            $payload = $request->only([
                'name','email','password','password_confirmation','phone_number','dob','avatar_url','role'
            ]);

            if (array_key_exists('name', $payload) && !is_null($payload['name'])) {
                $payload['name'] = trim((string)$payload['name']);
            }
            if (array_key_exists('email', $payload) && !is_null($payload['email'])) {
                $payload['email'] = strtolower(trim((string)$payload['email']));
            }
            if (array_key_exists('password', $payload) && !is_null($payload['password'])) {
                $payload['password'] = trim((string)$payload['password']);
            }
            if (array_key_exists('phone_number', $payload) && !is_null($payload['phone_number'])) {
                $payload['phone_number'] = trim((string)$payload['phone_number']);
            }
            if (array_key_exists('avatar_url', $payload) && !is_null($payload['avatar_url'])) {
                $payload['avatar_url'] = trim((string)$payload['avatar_url']);
            }
            if (array_key_exists('dob', $payload) && !is_null($payload['dob'])) {
                $payload['dob'] = trim((string)$payload['dob']);
            }
            if (array_key_exists('role', $payload) && !is_null($payload['role'])) {
                $payload['role'] = strtolower(trim((string)$payload['role']));
            }

            $validated = validator(
                $payload,
                [
                    'name'       => ['sometimes','required','string','max:255'],
                    'email'      => ['sometimes','required','string','email','max:255', 
                        Rule::unique('users','email')->ignore($user->id)],
                    'password'   => ['sometimes','required','confirmed','string','min:8','max:255'], 
                    'avatar_url' => ['sometimes','nullable','url','max:2048'],
                    'dob' => ['sometimes','nullable','date'],
                    'phone_number' => ['sometimes','nullable','string','max:15', 
                        Rule::unique('users','phone_number')->ignore($user->id)],
                    'role' => ['sometimes','string','in:user,admin'],
                ],
                [
                    'name.required'     => 'The name is required.',
                    'name.string'       => 'The name must be a string.',
                    'name.max'          => 'The name may not be greater than 255 characters.',

                    'email.required'    => 'The email is required.',
                    'email.string'      => 'The email must be a string.',
                    'email.email'       => 'The email is invalid.',
                    'email.max'         => 'The email may not be greater than 255 characters.',
                    'email.unique'      => 'The email has already been taken.',

                    'password.required' => 'The password is required.',
                    'password.string'   => 'The password must be a string.',
                    'password.min'      => 'The password must be at least :min characters.',
                    'password.max'      => 'The password may not be greater than :max characters.',
                    'password.confirmed'=> 'The password confirmation does not match.',

                    'avatar_url.url'    => 'The avatar URL must be a valid URL.',
                    'avatar_url.max'    => 'The avatar URL may not be greater than 2048 characters.',

                    'phone_number.string' => 'The phone number must be a string.',
                    'phone_number.max'    => 'The phone number may not be greater than 15 characters.',
                    'phone_number.unique' => 'The phone number has already been taken.',

                    'dob.date' => 'The date of birth must be a valid date.',

                    'role.in' => 'The role must be either user or admin.',
                    'role.string' => 'The role must be a string.',
                ]
            )->validate();

            if (empty($validated)) {
                return response()->json([
                    'status'=>'fail',
                    'message'=>'No fields to update.'
                ], 422);
            }

            if (array_key_exists('password', $validated)) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $user->fill($validated);

            $user->save();

            $user->load([
                'attempts',   
                'progress',
            ])->loadCount(['attempts','progress']);

            return response()->json([
                'status' => 'success',
                'data'   => $user->makeHidden(['password']),
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status'=>'fail',
                'message'=>'Validation error.',
                'errors'=>$e->errors()
            ], 422);
        } catch (\Throwable $e) {
            Log::error('User update error', ['id'=>$user->id ?? null, 'error'=>$e]);
            return response()->json([
                'status'=>'error',
                'message'=>'Cannot update user: '.$e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response | \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        try {
            if ($user->attempts()->exists()) {
                $user->delete();

                return response()->json([
                    'status'  => 'success',
                    'message' => 'User has related attempts → soft deleted (marked deleted_at).',
                ]);
            }

            $user->forceDelete();

            return response()->json([
                'status'  => 'success',
                'message' => 'User had no related attempts → permanently deleted.',
            ]);
        } catch (\Throwable $e) {
            Log::error('User destroy error', [
                'id' => $user->id ?? null,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot delete user: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);

            if ($user->trashed()) {
                $user->restore();

                return response()->json([
                    'status'  => 'success',
                    'message' => 'User restored successfully.',
                ]);
            }

            return response()->json([
                'status'  => 'info',
                'message' => 'User is already active (not deleted).',
            ]);
        } catch (\Throwable $e) {
            Log::error('User restore error', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot restore user: ' . $e->getMessage(),
            ], 500);
        }
    }
}
