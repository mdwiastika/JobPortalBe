<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CompanyController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            $role = $user->roles->first()->name;
            $companies = Company::latest()->get();
            return new CompanyResource('success', 'Data fetched successfully', $companies);
        } catch (\Throwable $th) {
            return new CompanyResource('error', $th->getMessage(), null);
        }
    }
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $role = $user->roles->first()->name;
            if ($role != 'super_admin' && $role != 'admin') {
                $company = Company::where('user_id', $user->id)->first();
                if ($company)
                    return new CompanyResource('error', 'You are not allowed to create more than one company', null);
            }
            $validatedData = $request->validate([
                'name' => 'required',
                'location' => 'required',
                'logo' => 'required',
                'industry' => 'required',
                'description' => 'required',
            ], [
                'name.required' => 'The name field is required.',
                'location.required' => 'The location field is required.',
                'logo.required' => 'The logo field is required.',
                'industry.required' => 'The industry field is required.',
                'description.required' => 'The description field is required.',
            ]);
            $path = $request->file('logo')->store('companies');
            $validatedData['logo'] = $path;
            $company = Company::create($validatedData);
            return new CompanyResource('success', 'Data stored successfully', $company);
        } catch (ValidationException $ve) {
            $errors = $ve->errors();
            $firstErrorMessages = array_values($errors)[0];
            return new CompanyResource('error', $firstErrorMessages, null);
        } catch (\Throwable $th) {
            return new CompanyResource('error', $th->getMessage(), null);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $company = Company::findOrFail($id);
            $validatedData = $request->validate([
                'name' => 'required',
                'location' => 'required',
                'industry' => 'required',
                'description' => 'required',
            ], [
                'name.required' => 'The name field is required.',
                'location.required' => 'The location field is required.',
                'industry.required' => 'The industry field is required.',
                'description.required' => 'The description field is required.',
            ]);
            if ($request->hasFile('logo')) {
                Storage::delete($company->logo);
                $path = $request->file('logo')->store('companies');
                $validatedData['logo'] = $path;
            }
            $company->update($validatedData);
            return new CompanyResource('success', 'Data updated successfully', $company);
        } catch (ModelNotFoundException $th) {
            return new CompanyResource('error', $th->getMessage(), null);
        } catch (ValidationException $ve) {
            $errors = $ve->errors();
            $firstErrorMessages = array_values($errors)[0];
            return new CompanyResource('error', $firstErrorMessages, null);
        } catch (\Throwable $th) {
            return new CompanyResource('error', $th->getMessage(), null);
        }
    }
    public function destroy($id)
    {
        try {
            $company = Company::findOrFail($id);
            Storage::delete($company->logo);
            $company->delete();
            return new CompanyResource('success', 'Data deleted successfully', null);
        } catch (\Throwable $th) {
            return new CompanyResource('error', $th->getMessage(), null);
        } catch (ModelNotFoundException $th) {
            return new CompanyResource('error', $th->getMessage(), null);
        }
    }
}
