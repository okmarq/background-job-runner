<?php

namespace App\Http\Controllers;

use App\Models\BackgroundJob;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class BackgroundJobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|Factory|Application
    {
        $status = request('status');
        $priority = request('priority');
        $jobLogs = $this->getJobLogs('background_jobs.log', $status, $priority);
        $errorLogs = $this->getJobLogs('background_jobs_errors.log');
        $activeJobs = BackgroundJob::where('status', config('constants.status.running'))->get();
        $jobs = BackgroundJob::query();
        if (isset($status)) $jobs->where('status', $status);
        if (isset($priority)) $jobs->where('priority', $priority);
        return view('pages.background_jobs.index', [
            'jobs' => $jobs->get(),
            'activeJobs' => $activeJobs,
            'jobLogs' => $jobLogs,
            'errorLogs' => $errorLogs,
        ]);
    }

    public function cancelJob(BackgroundJob $backgroundJob): RedirectResponse
    {
        if ($backgroundJob->status !== config('constants.status.running')) return redirect()->route('background_jobs.index')->with('error', 'Job has been cancelled.');
        // programmatically get the process id and kill it
        // Update job status
        $backgroundJob->update(['status' => config('constants.status.cancelled')]);
        return redirect()->route('background_jobs.index')->with('success', 'Job has been cancelled.');
    }

    public function reRunJob(BackgroundJob $backgroundJob): RedirectResponse
    {
        if ($backgroundJob->status === config('constants.status.failed') ||
            $backgroundJob->status === config('constants.status.cancelled')) {
            // the attempt will be the retry plus the number of each rerun
            // change the retry to 1 so it runs only once
            $attempt = $backgroundJob->retries;
            $backgroundJob->retries = 1;
            $backgroundJob->attempt += $attempt;
            $backgroundJob->status = config('constants.status.running');
            $backgroundJob->save();
            runBackgroundJob($backgroundJob);
            return redirect()->route('background_jobs.index')->with('success', 'Job is being processed.');
        }
        return redirect()->route('background_jobs.index')->with('error', 'Job is already running or processed.');

    }

    public function startJob(BackgroundJob $backgroundJob): RedirectResponse
    {
        if ($backgroundJob->status === config('constants.status.pending')) {
            runBackgroundJob($backgroundJob);
            $backgroundJob->update(['status' => config('constants.status.running')]);
            return redirect()->route('background_jobs.index')->with('success', 'Job processing has started.');
        }
        return redirect()->route('background_jobs.index')->with('error', "Can't start this Job.");
    }

    protected function getJobLogs($logFile, $status = null, $priority = null): false|array
    {
        $logs = file(storage_path("logs/{$logFile}"));
        return array_filter($logs, function ($log) use ($status, $priority) {
            $logData = json_decode($log, true);
            return (!$status || $logData['status'] === $status) && (!$priority || $logData['priority'] == $priority);
        });
    }
}
