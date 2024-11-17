<?php

namespace App\Http\Controllers;

use App\Http\Resources\JobPostingResource;
use App\Models\JobPosting;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class JobPostingController extends Controller
{
    public $validationMessage = [
        'recruiter_id.required' => 'The recruiter id field is required.',
        'recruiter_id.exists' => 'The recruiter id does not exist.',
        'title.required' => 'The title field is required.',
        'description.required' => 'The description field is required.',
        'requirements.required' => 'The requirements field is required.',
        'employment_type.required' => 'The employment type field is required.',
        'employment_type.in' => 'The employment type must be one of the following: full_time, part_time, contract, internship.',
        'experience_level.required' => 'The experience level field is required.',
        'experience_level.in' => 'The experience level must be one of the following: begineer, medium, expert.',
        'work_type.required' => 'The work type field is required.',
        'work_type.in' => 'The work type must be one of the following: on_site, remote, hybrid.',
        'min_salary.required' => 'The minimum salary field is required.',
        'min_salary.numeric' => 'The minimum salary must be a number.',
        'min_salary.min' => 'The minimum salary must be at least 0.',
        'min_salary.lt' => 'The minimum salary must be less than the maximum salary.',
        'max_salary.required' => 'The maximum salary field is required.',
        'max_salary.numeric' => 'The maximum salary must be a number.',
        'max_salary.min' => 'The maximum salary must be at least 0.',
        'max_salary.gte' => 'The maximum salary must be greater than or equal to the minimum salary.',
        'location.required' => 'The location field is required.',
    ];
    public function index()
    {
        try {
            $jobPostings = JobPosting::query()->latest()->get();
            return new JobPostingResource(200, 'Job postings retrieved successfully', $jobPostings);
        } catch (\Exception $e) {
            return new JobPostingResource(500, 'An error occurred', $e->getMessage());
        }
    }
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'recruiter_id' => 'required|exists:users,id',
                'title' => 'required',
                'description' => 'required',
                'requirements' => 'required',
                'employment_type' => 'required|in:full_time,part_time,contract,internship',
                'experience_level' => 'required|in:begineer,medium,expert',
                'work_type' => 'required|in:on_site,remote,hybrid',
                'min_salary' => 'required|numeric|min:0|lt:max_salary',
                'max_salary' => 'required|numeric|min:0|gte:min_salary',
                'location' => 'required',
            ], $this->validationMessage);
            $jobPosting = JobPosting::create($validatedData);
            return new JobPostingResource(201, 'Job posting created successfully', $jobPosting);
        } catch (\Exception $e) {
            return new JobPostingResource(500, 'An error occurred', $e->getMessage());
        } catch (ValidationException $ve) {
            $errors = $ve->errors();
            $firstErrorMessages = array_values($errors)[0];
            return new JobPostingResource(400, $firstErrorMessages, null);
        }
    }
    public function update(Request $request, string $id)
    {
        try {
            $jobPosting = JobPosting::findOrFail($id);
            $validatedData = $request->validate([
                'recruiter_id' => 'required|exists:users,id',
                'title' => 'required',
                'description' => 'required',
                'requirements' => 'required',
                'employment_type' => 'required|in:full_time,part_time,contract,internship',
                'experience_level' => 'required|in:begineer,medium,expert',
                'work_type' => 'required|in:on_site,remote,hybrid',
                'min_salary' => 'required|numeric|min:0|lt:max_salary',
                'max_salary' => 'required|numeric|min:0|gte:min_salary',
                'location' => 'required',
            ], $this->validationMessage);
            $jobPosting->update($validatedData);
            return new JobPostingResource(200, 'Job posting updated successfully', $jobPosting);
        } catch (ValidationException $ve) {
            $errors = $ve->errors();
            $firstErrorMessages = array_values($errors)[0];
            return new JobPostingResource(400, $firstErrorMessages, null);
        } catch (ModelNotFoundException $e) {
            return new JobPostingResource(404, 'Job posting not found', $e->getMessage());
        } catch (\Exception $e) {
            return new JobPostingResource(500, 'An error occurred', $e->getMessage());
        }
    }
    public function destroy(string $id)
    {
        try {
            $jobPosting = JobPosting::findOrFail($id);
            $jobPosting->delete();
            return new JobPostingResource(200, 'Job posting deleted successfully', null);
        } catch (ModelNotFoundException $e) {
            return new JobPostingResource(404, 'Job posting not found', $e->getMessage());
        } catch (\Exception $e) {
            return new JobPostingResource(500, 'An error occurred', $e->getMessage());
        }
    }
}
