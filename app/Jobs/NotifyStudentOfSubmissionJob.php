<?php

namespace App\Jobs;

use App\Mail\SubmissionConfirmed;
use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyStudentOfSubmissionJob implements ShouldQueue
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
        Mail::to($assignment->user->email)->send(new SubmissionConfirmed($assignment));
    }
}
