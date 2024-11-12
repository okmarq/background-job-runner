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
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $assignment = Assignment::findOrFail($this->assignmentId);
        $plagiarismChecker = new PlagiarismChecker();
        $result = $plagiarismChecker->check($assignment);
        $assignment->update([
            'plagiarism_score' => $result['score'],
        ]);
        if ($result['score'] < 50)
            Log::channel('plagiarism')->critical("Checked for plagiarism", [
                'assignment' => $assignment->id,
                'plagiarism_score' => $result['score'],
                'status' => $result['status'],
                'timestamp' => now()->toDateTimeString(),
            ]);
        else
            Log::channel('no_plagiarism')->error("Checked for plagiarism", [
                'assignment' => $assignment->id,
                'plagiarism_score' => $result['score'],
                'status' => $result['status'],
                'timestamp' => now()->toDateTimeString(),
            ]);
    }
}
