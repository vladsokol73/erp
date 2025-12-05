<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Services\Client\FailedJobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;
use Inertia\Response;

class FailedJobController extends Controller
{
    public function __construct(
        public readonly FailedJobService $failedJobService
    ) {}

    public function showFailedJobs(Request $request): Response
    {
        $page = $request->integer('page', 1);
        $perPage = $request->integer('perPage', 10);

        return Inertia::render('Clients/FailedJobs', [
            'jobs' => $this->failedJobService->getFailedJobsPaginated($page, $perPage),
        ]);
    }

    public function restartJob(int $failedJobId): JsonResponse
    {
        try {
            Artisan::call('queue:retry', ['id' => $failedJobId]);

            return ApiResponse::successMessage(
                "Job #$failedJobId restarted successfully."
            );
        } catch (\Throwable $e) {
            return ApiResponse::serverError("Failed to restart job #$failedJobId.");
        }
    }

    public function restartAll(): JsonResponse
    {
        try {
            Artisan::call('queue:retry', ['id' => 'all']);

            return ApiResponse::successMessage(
                "All failed jobs restarted successfully."
            );
        } catch (\Throwable $e) {
            return ApiResponse::serverError("Failed to restart all jobs.");
        }
    }
}
