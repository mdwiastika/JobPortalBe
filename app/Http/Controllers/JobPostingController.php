<?php

namespace App\Http\Controllers;

use App\Http\Resources\JobPostingResource;
use App\Models\JobPosting;
use Carbon\Carbon;
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
    public function index(Request $request)
    {
        try {
            if ($request->user()->hasRole('recruiter')) {
                $jobPostings = JobPosting::query()->where('recruiter_id', $request->user()->id)->with(['recruiter', 'skills', 'jobCategories'])->latest()->get();
            } else {
                $jobPostings = JobPosting::query()->with(['recruiter', 'skills', 'jobCategories'])->latest()->get();
            }
            return new JobPostingResource("success", 'Job postings retrieved successfully', $jobPostings);
        } catch (\Exception $e) {
            return new JobPostingResource("error", 'An error occurred', $e->getMessage());
        }
    }
    public function featuredJobs()
    {
        try {
            $jobPostings = JobPosting::query()->with(['recruiter', 'skills', 'jobCategories', 'recruiter.company'])->latest()->limit(8)->get();
            return new JobPostingResource("success", 'Featured job postings retrieved successfully', $jobPostings);
        } catch (\Exception $e) {
            return new JobPostingResource("error", 'An error occurred', $e->getMessage());
        }
    }
    public function searchJobs(Request $request)
    {
        try {
            $salaryRanges = [
                "Less than 1 million" => [null, 1000000],
                "1 million - 3 million" => [1000000, 3000000],
                "3 million - 5 million" => [3000000, 5000000],
                "5 million - 10 million" => [5000000, 10000000],
                "More than 10 million" => [10000000, null],
            ];
            $datePostingRange = [
                "Today" => [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()],
                "Yesterday" => [Carbon::yesterday()->startOfDay(), Carbon::yesterday()->endOfDay()],
                "Last 7 days" => [Carbon::now()->subDays(6)->startOfDay(), Carbon::now()->endOfDay()],
                "Last 30 days" => [Carbon::now()->subDays(29)->startOfDay(), Carbon::now()->endOfDay()],
                "All" => [null, null],
            ];
            $jobPostings = JobPosting::query();
            if ($request->filters['salary']) {
                $salaryRange = $salaryRanges[$request->filters['salary']];
                $jobPostings->whereBetween('min_salary', $salaryRange)->orWhereBetween('max_salary', $salaryRange);
            }
            if ($request->filters['date_posting']) {
                $dateRange = $datePostingRange[$request->filters['date_posting']];
                $jobPostings->whereBetween('created_at', $dateRange);
            }
            if ($request->filters['level_experience']) {
                $jobPostings->where('experience_level', $request->filters['level_experience']);
            }
            if ($request->filters['location']) {
                $jobPostings->where('location', 'like', '%' . $request->filters['location'] . '%');
            }
            if ($request->filters['job_category']) {
                $jobPostings->whereHas('jobCategories', function ($query) use ($request) {
                    $query->where('slug_category', $request->filters['job_category']);
                });
            }
            if ($request->filters['type_job']) {
                $jobPostings->where('employment_type', $request->filters['type_job']);
            }
            if ($request->filters['work_type']) {
                $jobPostings->where('work_type', $request->filters['work_type']);
            }


            $jobPostings = $jobPostings->with(['recruiter', 'skills', 'jobCategories', 'recruiter.company'])->latest()->paginate(10);
            return new JobPostingResource("success", 'Job postings retrieved successfully', $jobPostings);
        } catch (\Exception $e) {
            return new JobPostingResource("error", 'An error occurred', $e->getMessage());
        }
    }
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required',
                'description' => 'required',
                'requirements' => 'required',
                'employment_type' => 'required|in:full_time,part_time,contract,internship',
                'experience_level' => 'required|in:beginner,medium,expert',
                'work_type' => 'required|in:on_site,remote,hybrid',
                'min_salary' => 'required|numeric|min:0|lt:max_salary',
                'max_salary' => 'required|numeric|min:0|gte:min_salary',
                'location' => 'required',
                'is_disability' => 'boolean',
            ], $this->validationMessage);
            if ($request->recruiter_id && ($request->user()->hasRole('admin') || $request->user()->hasRole('super_admin'))) {
                $validatedData['recruiter_id'] = $request->recruiter_id;
            } else {
                $validatedData['recruiter_id'] = $request->user()->id;
            }
            $jobPosting = JobPosting::create($validatedData);
            $skills = collect(json_decode($request->skills))->pluck('id')->toArray();
            $jobCategories = collect(json_decode($request->job_categories))->pluck('id')->toArray();
            $jobPosting->skills()->sync($skills);
            $jobPosting->jobCategories()->sync($jobCategories);
            $jobPosting->load(['recruiter', 'skills', 'jobCategories']);
            return new JobPostingResource("success", 'Job posting created successfully', $jobPosting);
        } catch (ValidationException $ve) {
            $errors = $ve->errors();
            $firstErrorMessages = array_values($errors)[0];
            return new JobPostingResource("error", $firstErrorMessages, null);
        } catch (\Exception $e) {
            return new JobPostingResource("error", 'An error occurred', $e->getMessage());
        }
    }
    public function update(Request $request, string $id)
    {
        try {
            $jobPosting = JobPosting::findOrFail($id);
            $validatedData = $request->validate([
                'title' => 'required',
                'description' => 'required',
                'requirements' => 'required',
                'employment_type' => 'required|in:full_time,part_time,contract,internship',
                'experience_level' => 'required|in:beginner,medium,expert',
                'work_type' => 'required|in:on_site,remote,hybrid',
                'min_salary' => 'required|numeric|min:0|lt:max_salary',
                'max_salary' => 'required|numeric|min:0|gte:min_salary',
                'location' => 'required',
                'is_disability' => 'boolean',
            ], $this->validationMessage);
            if ($request->recruiter_id) {
                $validatedData['recruiter_id'] = $request->recruiter_id;
            } else {
                $validatedData['recruiter_id'] = $request->user()->id;
            }
            $jobPosting->update($validatedData);
            $skills = collect(json_decode($request->skills))->pluck('id')->toArray();
            $jobCategories = collect(json_decode($request->job_categories))->pluck('id')->toArray();
            $jobPosting->skills()->sync($skills);
            $jobPosting->jobCategories()->sync($jobCategories);
            $jobPosting = JobPosting::with(['recruiter', 'skills', 'jobCategories'])->findOrFail($id);
            return new JobPostingResource("success", 'Job posting updated successfully', $jobPosting);
        } catch (ValidationException $ve) {
            $errors = $ve->errors();
            $firstErrorMessages = array_values($errors)[0];
            return new JobPostingResource("error", $firstErrorMessages, null);
        } catch (ModelNotFoundException $e) {
            return new JobPostingResource("error", 'Job posting not found', $e->getMessage());
        } catch (\Exception $e) {
            return new JobPostingResource("error", 'An error occurred', $e->getMessage());
        }
    }
    public function destroy(string $id)
    {
        try {
            $jobPosting = JobPosting::findOrFail($id);
            $jobPosting->delete();
            return new JobPostingResource("success", 'Job posting deleted successfully', null);
        } catch (ModelNotFoundException $e) {
            return new JobPostingResource("error", 'Job posting not found', $e->getMessage());
        } catch (\Exception $e) {
            return new JobPostingResource("error", 'An error occurred', $e->getMessage());
        }
    }
}
