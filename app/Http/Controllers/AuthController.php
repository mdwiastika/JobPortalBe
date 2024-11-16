<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'full_name' => 'required|max:55',
                'email' => 'email|required|unique:users',
                'password' => 'required|confirmed',
                'roles' => 'required',
            ]);
            $validatedData['password'] = bcrypt($request->password);
            $user = User::create($validatedData);
            $user->assignRole($request->roles);
            $token = $user->createToken('auth_token')->plainTextToken;
            $user['token'] = $token;
            return new UserResource('success', 'Successfully Register User!', $user);
        } catch (\Throwable $th) {
            return new UserResource('error', $th->getMessage(), null);
        } catch (ValidationException $ve) {
            return new UserResource('error', $ve->errors(), null);
        }
    }
    public function login(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'email' => 'email|required',
                'password' => 'required',
            ]);
            if (!Auth::attempt($validatedData)) {
                return new UserResource('error', 'Unauthorized', null);
            }
            $user = User::where('email', $validatedData['email'])->firstOrFail();
            $token = $user->createToken('auth_token')->plainTextToken;
            $user['token'] = $token;
            return new UserResource('success', 'Successfully Login User!', $user);
        } catch (\Throwable $th) {
            return new UserResource('error', $th->getMessage(), null);
        } catch (ValidationException $ve) {
            return new UserResource('error', $ve->errors(), null);
        }
    }
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return new UserResource('success', 'Successfully Logout User!', null);
        } catch (\Throwable $th) {
            return new UserResource('error', $th->getMessage(), null);
        }
    }
}
