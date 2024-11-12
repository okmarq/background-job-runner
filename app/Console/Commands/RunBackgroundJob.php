<?php

namespace App\Console\Commands;

use App\Models\BackgroundJob;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\Log;

class RunBackgroundJob extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'background:run {backgroundJob}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a background job';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $id = $this->argument('backgroundJob');
            $job = BackgroundJob::findOrFail($id);
            if ($job->delay > 0) sleep($job->delay);
            $job->update([
                'status' => config('constants.status.running'),
            ]);
            $this->process($job, $job->retries);
        } catch (Exception $e) {
            $this->error($e->getMessage());
            Log::channel('background_jobs_errors')->error("Job failed", [
                'error' => $e->getMessage(),
                'status' => config('constants.status.failed')
            ]);
        }
    }

    public function process(BackgroundJob $job, int $retries): ?BackgroundJob
    {
        try {
//            $queue = match ($job->priority) {
//                1 => config('constants.queue.priority.high', 'high'),
//                2 => config('constants.queue.priority.medium', 'medium'),
//                default => config('constants.queue.priority.low', 'low'),
//            };
//            dispatch((new $job->class(...json_decode($job->parameters)))->onQueue($queue));
            $instance = new $job->class(...json_decode($job->parameters));
            $result = call_user_func_array([$instance, $job->method], json_decode($job->parameters));
            $attempt = $job->attempt += 1;
            $job->update([
                'output' => $result != null ? json_encode($result) : null,
                'attempt' => $attempt,
                'status' => config('constants.status.completed'),
            ]);
            Log::channel('background_jobs')->info("Job {$job->id} completed: {$job->class}@{$job->method}", [
                'parameters' => $job->parameters,
                'result' => $result,
                'status' => config('constants.status.completed')
            ]);
            $this->info("Job {$job->id}: {$job->class}@{$job->method} executed successfully.");
            return $job;
        } catch (Exception $e) {
            if ($retries > 0) {
                $this->info("Retrying job {$job->id}: {$job->class}@{$job->method}");
                $this->process($job, $retries - 1);
            } else {
                $this->error("Job {$job->id}: {$job->class}@{$job->method} failed: " . $e->getMessage());
                $job->update([
                    'output' => $e->getMessage(),
                    'status' => config('constants.status.failed'),
                ]);
                Log::channel('background_jobs_errors')->error("Job {$job->id} failed: {$job->class}@{$job->method}", [
                    'parameters' => $job->parameters,
                    'error' => $e->getMessage(),
                    'status' => config('constants.status.failed')
                ]);
            }
            return null;
        }
    }
}
