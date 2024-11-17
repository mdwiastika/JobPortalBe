<?php

namespace App\Http\Controllers;

use App\Http\Resources\JobCategoryResource;
use App\Models\JobCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class JobCategoryController extends Controller
{
    public function index()
    {
        try {
            $jobCategories = JobCategory::latest()->get();
            return new JobCategoryResource('success', 'Job categories retrieved successfully', $jobCategories);
        } catch (\Throwable $th) {
            return new JobCategoryResource('error', $th->getMessage(), null);
        }
    }
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'category_name' => 'required',
                'icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:9048',
            ], [
                'category_name.required' => 'The category name field is required.',
                'icon.required' => 'The icon field is required.',
                'icon.image' => 'The icon must be an image.',
                'icon.mimes' => 'The icon must be a file of type: jpeg, png, jpg, gif, svg.',
                'icon.max' => 'The icon may not be greater than 9048 kilobytes.',
            ]);
            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('job_categories');
                $validatedData['icon'] = $path;
            }
            $jobCategory = JobCategory::create($validatedData);
            return new JobCategoryResource('success', 'Job category created successfully', $jobCategory);
        } catch (\Throwable $th) {
            return new JobCategoryResource('error', $th->getMessage(), null);
        } catch (ValidationException $ve) {
            $errors = $ve->errors();
            $firstErrorMessages = array_values($errors)[0];
            return new JobCategoryResource('error', $firstErrorMessages, null);
        }
    }
    public function update(Request $request, JobCategory $jobCategory)
    {
        try {
            $validatedData = $request->validate([
                'category_name' => 'required',
            ], [
                'category_name.required' => 'The category name field is required.',
            ]);
            if ($request->hasFile('icon')) {
                $validatedData = $request->validate([
                    'icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:9048',
                ], [
                    'icon.required' => 'The icon field is required.',
                    'icon.image' => 'The icon must be an image.',
                    'icon.mimes' => 'The icon must be a file of type: jpeg, png, jpg, gif, svg.',
                    'icon.max' => 'The icon may not be greater than 9048 kilobytes.',
                ]);
                Storage::delete($jobCategory->icon);
                $path = $request->file('icon')->store('job_categories');
                $validatedData['icon'] = $path;
            }
            $jobCategory->update($validatedData);
            return new JobCategoryResource('success', 'Job category updated successfully', $jobCategory);
        } catch (ValidationException $ve) {
            $errors = $ve->errors();
            $firstErrorMessages = array_values($errors)[0];
            return new JobCategoryResource('error', $firstErrorMessages, null);
        } catch (\Throwable $th) {
            return new JobCategoryResource('error', $th->getMessage(), null);
        }
    }
    public function destroy(JobCategory $jobCategory)
    {
        try {
            Storage::delete($jobCategory->icon);
            $jobCategory->delete();
            return new JobCategoryResource('success', 'Job category deleted successfully', null);
        } catch (\Throwable $th) {
            return new JobCategoryResource('error', $th->getMessage(), null);
        }
    }
}
