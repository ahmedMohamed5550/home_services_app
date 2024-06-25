<?php

namespace App\Services;

use App\Models\Feedback;
use Illuminate\Support\Facades\DB;

class FeedbackService
{
    public function getAverageRatingPerEmployee($employeeId)
    {
        // Retrieve rating counts for the specific employee
        $averageRating = Feedback::where('employee_id', $employeeId)
            ->select(DB::raw('SUM(rating) / COUNT(*) AS average_rating'))
            ->groupBy('employee_id')
            ->first(); // Use first() instead of get() to get a single record

        // Extract the average rating value and format it to one decimal place
        $averageRatingValue = $averageRating ? number_format($averageRating->average_rating, 1) : null;

        return ['average_rating' => $averageRatingValue];
    }
}
