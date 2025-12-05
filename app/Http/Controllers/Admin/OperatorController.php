<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleEnum;
use App\Facades\Guard;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateChannelRequest;
use App\Http\Requests\Admin\UpdateOperatorRequest;
use App\Http\Responses\ApiResponse;
use App\Services\ChannelService;
use App\Services\Operator\AiRetentionReportService;
use App\Services\Operator\OperatorService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OperatorController extends Controller
{
    public function __construct(
        public readonly OperatorService $operatorService,
        public readonly ChannelService $channelService,
        public readonly AiRetentionReportService $aiRetentionReportService,
    ) {}

    public function showOperators(Request $request): Response
    {
        if (!Guard::role()->hasRole(RoleEnum::ADMIN->value)) {
            return Inertia::render('Error/403');
        }

        $operatorPage = $request->integer('operatorPage', 1);
        $operatorSearch = $request->string('operatorSearch');
        $operatorPerPage = $request->integer('operatorPerPage', 10);
        $channelPage = $request->integer('channelPage', 1);
        $channelSearch = $request->string('channelSearch');
        $channelPerPage = $request->integer('channelPerPage', 10);

        return Inertia::render('AdminPanel/Operators',
        [
            'operators' => $this->operatorService->getOperatorsPaginated(page: $operatorPage,search: $operatorSearch, perPage: $operatorPerPage),
            'channels' => $this->channelService->getChannelsPaginated(page: $channelPage,search: $channelSearch, perPage: $channelPerPage),
        ]);
    }

    public function editOperator(UpdateOperatorRequest $request, int $operatorId): JsonResponse
    {
        if (!Guard::role()->hasRole(RoleEnum::ADMIN->value)) {
            return ApiResponse::forbidden('Access denied.');
        }

        try {
            return ApiResponse::success(
                [
                    "operator" => $this->operatorService->editOperator(
                        name: $request->string('name'),
                        operatorId: $operatorId,
                        hasAiRetention: $request->boolean('has_ai_retention')
                    )
                ],
                'Operator successfully updated.'
            );
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound('Operator not found.');
        }
    }


    public function deleteOperator(int $operatorId): JsonResponse
    {
        if (!Guard::role()->hasRole(RoleEnum::ADMIN->value)) {
            return ApiResponse::forbidden('Access denied.');
        }
        try {
            $this->operatorService->deleteOperator($operatorId);

            return ApiResponse::success('Operator successfully updated.');
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound('Operator not found.');
        }
    }

    public function editChannel(UpdateChannelRequest $request, int $channelId): JsonResponse
    {
        if (!Guard::role()->hasRole(RoleEnum::ADMIN->value)) {
            return ApiResponse::forbidden('Access denied.');
        }

        try {
            return ApiResponse::success(
                [
                    "channel" => $this->channelService->editChannel(
                        name: $request->string('name'),
                        channelId: $channelId,
                        hasAiRetention: $request->boolean('has_ai_retention')
                    )
                ],
                'Channel successfully updated.'
            );
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound('Channel not found.');
        }
    }


    public function deleteChannel(int $channelId): JsonResponse
    {
        if (!Guard::role()->hasRole(RoleEnum::ADMIN->value)) {
            return ApiResponse::forbidden('Access denied.');
        }

        try {
            $this->channelService->deleteChannel($channelId);

            return ApiResponse::success('Channel successfully updated.');
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound('Channel not found.');
        }
    }
}
