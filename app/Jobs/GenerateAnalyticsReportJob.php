<?php

namespace App\Jobs;

use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateAnalyticsReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected string $course)
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $assignments = Assignment::where('course', $this->course)->get();
        // Example analytics generation
        $averageScore = $assignments->avg('score');
        Log::channel('analytics')->info('Generated analytics report', [
            'course' => $this->course,
            'average_score' => $averageScore,
            'assignment_count' => $assignments->count(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}
