<?php

namespace App\Services;

use App\Models\Assignment;

class PlagiarismChecker
{
    public function check(Assignment $assignment): array
    {
        $remark = "The $assignment->course assignment was: ";
        $remark2 = " with a score of: ";
        $score = rand(0, 100);
        $status = $score < 50 ? 'not plagiarized' : 'plagiarized';
        return [
            'score' => $score,
            'status' => $remark . $status . $remark2 . $score,
        ];
    }
}
