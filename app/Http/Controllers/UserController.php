<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Mockery\Matcher\Not;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::whereDoesntHave('roles', function ($query) {
                $query->where('name', 'super_admin');
            })->with(['roles'])->get();
            return new UserResource('success', 'Data fetched successfully', $users);
        } catch (\Throwable $th) {
            return new UserResource('error', $th->getMessage(), null);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $validatedData = $request->validate([
                'full_name' => 'required',
                'email' => 'required|email|unique:users,email,' . $id,
                'password' => 'nullable|confirmed|min:8',
                'roles' => 'required',
            ]);
            $user->update($validatedData);
            $user->syncRoles($validatedData['roles']);
            return new UserResource('success', 'Data updated successfully', $user);
        } catch (ValidationException $ve) {
            $errors = $ve->errors();
            $firstErrorMessages = array_values($errors)[0];
            return new UserResource('error', $firstErrorMessages, null);
        } catch (\Throwable $th) {
            return new UserResource('error', $th->getMessage(), null);
        } catch (ModelNotFoundException $th) {
            return new UserResource('error', $th->getMessage(), null);
        }
    }
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return new UserResource('success', 'Data deleted successfully', null);
        } catch (ModelNotFoundException $th) {
            return new UserResource('error', $th->getMessage(), null);
        } catch (\Throwable $th) {
            return new UserResource('error', $th->getMessage(), null);
        }
    }
}
