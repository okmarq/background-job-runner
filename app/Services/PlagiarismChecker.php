<?php

namespace App\Services;

class PlagiarismChecker
{
    public function check(): array
    {
        $score = rand(0, 100);
        $status = $score < 50 ? 'No plagiarism' : 'Plagiarism';
        return [
            'score' => $score,
            'status' => $status,
        ];
    }
}
