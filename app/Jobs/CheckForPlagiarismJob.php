<?php

namespace App\Jobs;

use App\Models\Assignment;
use App\Services\PlagiarismChecker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckForPlagiarismJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected int $assignmentId)
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $assignment = Assignment::findOrFail($this->assignmentId);
        $plagiarismChecker = new PlagiarismChecker();
        $result = $plagiarismChecker->check();
        $assignment->update([
            'plagiarism_score' => $result['score'],
        ]);
        Log::channel('plagiarism')->info("Checked for plagiarism", [
            'assignment' => $assignment->id,
            'plagiarism_score' => $result['score'],
            'status' => $result['status']
        ]);
    }
}
