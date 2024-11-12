<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssignmentRequest;
use App\Jobs\AutomatedGradingJob;
use App\Jobs\CheckForPlagiarismJob;
use App\Jobs\GenerateAnalyticsReportJob;
use App\Jobs\NotifyStudentOfSubmissionJob;
use App\Models\Assignment;
use App\Models\BackgroundJob;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Application|Factory|View
    {
        $user = Auth::user();
        $assignments = $user->hasRole(config('constants.role.instructor')) ? Assignment::all() : $user->assignments()->orderBy('created_at', 'desc')->get();
        return view('pages.assignments.index', [
            'assignments' => $assignments
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Application|Factory|View
    {
        return view('pages.assignments.create', ['courses' => config('constants.course')]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssignmentRequest $request): RedirectResponse
    {
        try {
            $user = Auth::user();
            $assignment = $user->assignments()->create($request->validated());
        } catch (Exception $e) {
            Log::error("Error submitting assignment: {$e->getMessage()}", $e->getTrace());
            return redirect()->route('assignments.index')->with('error', 'Failed to submit Assignment, try again later.');
        }
        try {
            DB::beginTransaction();
            // High priority to provide fast feedback
            $automatedGradingJob = BackgroundJob::create([
                'class' => AutomatedGradingJob::class,
                'method' => 'handle',
                'parameters' => json_encode([$assignment->id])
            ]);
            // Background job to notify student of submission
            $notifyStudentOfSubmissionJob = BackgroundJob::create([
                'class' => NotifyStudentOfSubmissionJob::class,
                'method' => 'handle',
                'parameters' => json_encode([$assignment->id]),
                'retries' => 2,
                'priority' => 2
            ]);
            // Background job to generate analytics report (delayed, low priority)
            $generateAnalyticsReportJob = BackgroundJob::create([
                'class' => GenerateAnalyticsReportJob::class,
                'method' => 'handle',
                'parameters' => json_encode([$assignment->course]),
                'retries' => 2,
                'delay' => 15,
                'priority' => 3
            ]);
            // Background job for plagiarism check
            // Delay allows the document to be saved first
            $checkForPlagiarismJob = BackgroundJob::create([
                'class' => CheckForPlagiarismJob::class,
                'method' => 'handle',
                'parameters' => json_encode([$assignment->id]),
                'retries' => 2,
                'delay' => 10
            ]);
            DB::commit();
            runBackgroundJob($automatedGradingJob);
            runBackgroundJob($notifyStudentOfSubmissionJob);
            runBackgroundJob($generateAnalyticsReportJob);
            runBackgroundJob($checkForPlagiarismJob);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error processing assignment: {$e->getMessage()}", $e->getTrace());
            return redirect()->route('assignments.index')->with('success', 'Assignment Submitted successfully. Processing will take a little while longer.');
        }
        return redirect()->route('assignments.index')->with('success', 'Assignment Submitted successfully.');
    }
}
