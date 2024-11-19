<?php

namespace App\Http\Controllers;

use App\Http\Resources\SavedJobsResource;
use App\Models\SavedJob;
use Illuminate\Http\Request;

class SavedJobsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $savedJobs = SavedJob::where('job_seeker_id', $request->user()->id)->with(['jobPosting'])->get();
            return new SavedJobsResource('success', 'Saved jobs retrieved successfully', $savedJobs);
        } catch (\Throwable $th) {
            return new SavedJobsResource('error', $th->getMessage(), null);
        }
    }
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'job_posting_id' => 'required',
            ]);
            $validatedData['job_seeker_id'] = $request->user()->id;
            $savedJob = SavedJob::create($validatedData);
            $savedJob->load('jobPosting');
            return new SavedJobsResource('success', 'Job saved successfully', $savedJob);
        } catch (\Throwable $th) {
            return new SavedJobsResource('error', $th->getMessage(), null);
        }
    }
    public function savedJobs(Request $request)
    {
        try {
            $savedJobs = SavedJob::where('job_seeker_id', $request->user()->id)->where('job_posting_id', $request->job_posting_id)->with(['jobPosting'])->first();
            return new SavedJobsResource('success', 'Saved jobs retrieved successfully', $savedJobs);
        } catch (\Throwable $th) {
            return new SavedJobsResource('error', $th->getMessage(), null);
        }
    }
    public function destroy(Request $request, $id)
    {
        try {
            $savedJob = SavedJob::where('job_seeker_id', $request->user()->id)->where('id', $id)->first();
            $savedJob->delete();
            return new SavedJobsResource('success', 'Job unsaved successfully', null);
        } catch (\Throwable $th) {
            return new SavedJobsResource('error', $th->getMessage(), null);
        }
    }
}
