<?php

namespace App\Http\Controllers;

use App\Http\Resources\JobApplicationResource;
use App\Models\JobApplication;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class JobApplicationController extends Controller
{
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
}
