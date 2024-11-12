<?php

namespace App\Jobs;

use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutomatedGradingJob implements ShouldQueue
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
        // Sample grading logic (e.g., matching answers for objective questions)
        $score = $this->calculateScore();
        $assignment->update([
            'score' => $score,
        ]);
        Log::channel('assignment_processing')->info("Assignment graded", [
            'assignment' => $assignment->id,
            'course' => $assignment->course,
            'score' => $score,
            'user' => $assignment->user->fullName(),
        ]);
    }

    private function calculateScore(): int
    {
        return rand(0, 100);
    }
}
