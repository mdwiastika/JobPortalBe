<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReviewResource;
use App\Models\JobReview;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public $arrValidatedMessages = [
        'job_posting_id.required' => 'Job posting id is required',
        'rating.required' => 'Rating is required',
        'review_text.required' => 'Review text is required',
    ];
    public function index(Request $request)
    {
        try {
            $reviews = JobReview::query()->where('job_posting_id', $request->job_posting_id)->with(['user'])->get();
            return new ReviewResource('success', 'Reviews fetched successfully', $reviews);
        } catch (\Throwable $th) {
            return new ReviewResource('error', $th->getMessage(), null);
        }
    }
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'job_posting_id' => 'required',
                'rating' => 'required',
                'review_text' => 'required',
            ], $this->arrValidatedMessages);
            $validatedData['job_seeker_id'] = $request->user()->id;
            $review = JobReview::create($validatedData);
            $review->load('user');
            return new ReviewResource('success', 'Review added successfully', $review);
        } catch (ValidationException $ve) {
            $errors = $ve->errors();
            $firstErrorMessages = array_values($errors)[0];
            return new ReviewResource('error', $firstErrorMessages, null);
        } catch (\Throwable $th) {
            return new ReviewResource('error', $th->getMessage(), null);
        }
    }
}
