<?php

namespace App\Services\Operator;

use App\DTO\OperatorDto;
use App\DTO\OperatorListDto;
use App\DTO\PaginatedListDto;
use App\Models\Operator\Operator;

class OperatorService
{
    public function getOperators(): array
    {
        return OperatorDto::fromCollection(
            Operator::query()->get(['operator_id', 'name'])
        );
    }

    public function getOperatorById(int $operatorId): Operator
    {
        return Operator::query()->findOrFail($operatorId);
    }

    public function getOperatorsPaginated(int $page, string $search, int $perPage): PaginatedListDto
    {
        $query = Operator::query()
        ->search($search);

        return PaginatedListDto::fromPaginator(
            $query->paginate(perPage: $perPage, page: $page),
            fn($operator) => OperatorListDto::fromModel($operator)
        );
    }

    public function editOperator(string $name, int $operatorId, bool $hasAiRetention): OperatorListDto
    {
        $operator = $this->getOperatorById($operatorId);
        $operator->name = $name;
        $operator->setFlag('ai_retention', $hasAiRetention);
        $operator->save();

        return OperatorListDto::fromModel($operator);
    }

    public function deleteOperator(int $operatorId): void
    {
        $operator = $this->getOperatorById($operatorId);
        $operator->delete();
    }
}
