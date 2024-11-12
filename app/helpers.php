<?php

use App\Models\BackgroundJob;
use App\Services\JobValidator;
use Illuminate\Support\Facades\Log;

if (!function_exists('runBackgroundJob')) {
    function runBackgroundJob(BackgroundJob $job): void
    {
        $pid = null;
        try {
            (new JobValidator())->validate($job->class, $job->method);
            /*
             * php artisan background:run <job id>
             * php artisan background:run <job id>
             */
            $output = shell_exec("start /B " . 'php -v');
            Log::critical($output);
            $cmd = "php " . base_path("artisan") . " background:run " . $job->id . " > /dev/null 2>&1 & echo $!";
            // Run command in a background process
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $pid = shell_exec("start /B " . $cmd);
            } else {
                $pid = shell_exec($cmd);
            }
            Log::debug("Running command: $cmd");
            Log::debug("Command output: $pid");
        } catch (Exception $e) {
            Log::error('validation failed: ' . $e->getMessage(), $e->getTrace());
            Log::channel('background_jobs_errors')->error("Job {$job->id} failed: {$job->class}@{$job->method}", [
                'pid' => $pid,
                'parameters' => json_decode($job->parameters),
                'error' => $e->getMessage(),
                'status' => config('constants.status.failed')
            ]);
        }
    }
}

if (!function_exists('cancelJob')) {
    // if there's a pid, use it to terminate the process
    function cancelJob($pid): void
    {
        // Terminate the process based on OS
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            exec("taskkill /F /PID $pid");
        } else {
            exec("kill -9 $pid");
        }
    }
}
