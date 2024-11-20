<?php

namespace App\Http\Controllers;

use App\Http\Resources\JobApplicationResource;
use App\Models\JobApplication;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class JobApplicationController extends Controller
{
    public $arrValidatedMessage = [
        'job_posting_id.required' => 'Job posting ID is required',
        'job_posting_id.exists' => 'Job posting ID does not exist',
        'job_seeker_id.required' => 'Job seeker ID is required',
        'job_seeker_id.exists' => 'Job seeker ID does not exist',
        'cover_letter.required' => 'Cover letter is required',
        'resume.required' => 'Resume is required',
    ];
    public function index()
    {
        try {
            $jobApplications = JobApplication::query()->with(['jobPosting', 'applicant'])->latest()->get();
            return new JobApplicationResource("success", 'Job applications retrieved successfully', $jobApplications);
        } catch (\Exception $e) {
            return new JobApplicationResource("error", 'An error occurred', $e->getMessage());
        }
    }
    public function addNotes(Request $request, $id)
    {
        try {
            $jobApplication = JobApplication::query()->findOrFail($id);
            $validatedData = $request->validate([
                'interview_notes' => 'required|string',
            ]);
            $jobApplication->interview_notes = $validatedData['interview_notes'];
            $jobApplication->save();
            return new JobApplicationResource("success", 'Interview notes added successfully', $jobApplication);
        } catch (ModelNotFoundException $e) {
            return new JobApplicationResource("error", 'Job application not found', $e->getMessage());
        } catch (ValidationException $e) {
            $errors = $e->errors();
            $singleError = array_values($errors)[0];
            return new JobApplicationResource("error", $singleError, null);
        } catch (\Exception $e) {
            return new JobApplicationResource("error", 'An error occurred', $e->getMessage());
        }
    }
    public function changeStatus(Request $request, $id)
    {
        try {
            $jobApplication = JobApplication::query()->findOrFail($id);
            $validatedData = $request->validate([
                'status' => 'required|in:applied,interview,rejected,hired',
            ]);
            $jobApplication->status = $validatedData['status'];
            $jobApplication->save();
            return new JobApplicationResource("success", 'Status changed successfully', $jobApplication);
        } catch (ModelNotFoundException $e) {
            return new JobApplicationResource("error", 'Job application not found', $e->getMessage());
        } catch (\Exception $e) {
            return new JobApplicationResource("error", 'An error occurred', $e->getMessage());
        }
    }
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'job_posting_id' => 'required|exists:job_postings,id',
                'cover_letter' => 'required|file',
                'resume' => 'required|file',
            ], $this->arrValidatedMessage);
            if ($request->hasFile('cover_letter')) {
                $path = $request->file('cover_letter')->store('job_applications');
                $validatedData['cover_letter'] = $path;
            }
            if ($request->hasFile('resume')) {
                $path = $request->file('resume')->store('job_applications');
                $validatedData['resume'] = $path;
            }
            $validatedData['status'] = 'applied';
            $validatedData['job_seeker_id'] = $request->user()->id;
            $jobApplication = JobApplication::create($validatedData);
            return new JobApplicationResource('success', 'Job application created successfully', $jobApplication);
        } catch (ValidationException $ve) {
            $errors = $ve->errors();
            $firstMessageError = array_values($errors)[0];
            return new JobApplicationResource('error', $firstMessageError, null);
        } catch (\Throwable $th) {
            return new JobApplicationResource('error', $th->getMessage(), null);
        }
    }
}
