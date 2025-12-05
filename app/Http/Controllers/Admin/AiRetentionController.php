<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Facades\Guard;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EditChannelPromptRequest;
use App\Http\Responses\ApiResponse;
use App\Services\Operator\AiRetentionReportService;
use App\Services\SettingsService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class AiRetentionController extends Controller
{
    public function __construct(
        public readonly AiRetentionReportService $aiRetentionReportService,
        public readonly SettingsService $settingsService,
    ) {}

    public function showTestReports(Request $request): Response
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::AI_RETENTION_SHOW->value)) {
            return Inertia::render('Error/403');
        }

        $page = $request->integer('page', 1);
        $search = $request->string('search');

        return Inertia::render('AdminPanel/AiRetentions', [
            'reports' => $this->aiRetentionReportService->getTestReportsPaginated($page, $search),
            'prompt' => $this->settingsService->get('channel_prompt'),
        ]);
    }

    public function editPrompt(EditChannelPromptRequest $request): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::AI_RETENTION_SHOW->value)) {
            return ApiResponse::forbidden('Access denied.');
        }

        try {
            $this->settingsService->set(
                'channel_prompt',
                $request->validated('prompt')
            );

            return ApiResponse::success(
                'Prompt successfully updated.'
            );
        } catch (Throwable) {
            return ApiResponse::error('Failed to update prompt.');
        }
    }

    public function testPromptReport(): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::AI_RETENTION_SHOW->value)) {
            return ApiResponse::forbidden('Access denied.');
        }

        try {
            $this->aiRetentionReportService->generateTestReports(userId: auth()->id());
            return ApiResponse::success();
        } catch (Exception) {
            return ApiResponse::serverError('Failed to test prompt reports.');
        }
    }

    public function processJobReport(): JsonResponse
    {
        if (!Guard::permission()->hasPermission(PermissionEnum::AI_RETENTION_SHOW->value)) {
            return ApiResponse::forbidden('Access denied.');
        }

        try {
            $this->aiRetentionReportService->startReportJob();
            return ApiResponse::success();
        } catch (Exception) {
            return ApiResponse::serverError('Failed to start reports job.');
        }
    }
}
