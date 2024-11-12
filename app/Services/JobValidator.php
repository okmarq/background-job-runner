<?php

namespace App\Services;

use App\Jobs\AutomatedGradingJob;
use App\Jobs\CheckForPlagiarismJob;
use App\Jobs\GenerateAnalyticsReportJob;
use App\Jobs\NotifyStudentOfSubmissionJob;
use Exception;

class JobValidator
{
    protected array $allowedClasses = [
        AutomatedGradingJob::class => ['handle'],
        NotifyStudentOfSubmissionJob::class => ['handle'],
        GenerateAnalyticsReportJob::class => ['handle'],
        CheckForPlagiarismJob::class => ['handle'],
    ];

    /**
     * @throws Exception
     */
    public function validate(string $class, string $method): void
    {
        if (!array_key_exists($class, $this->allowedClasses)) throw new Exception("Unauthorized class: {$class}");
        if (!in_array($method, $this->allowedClasses[$class], true)) throw new Exception("Unauthorized method {$method} in class {$class}");
        if (!method_exists($class, $method)) throw new Exception("Method {$method} does not exist in class {$class}");
        if (!ctype_alnum(str_replace(['\\', '_'], '', $class)) || !ctype_alnum($method)) throw new Exception("Invalid characters detected in class or method name.");
    }
}
