<?php

namespace App\Services;

use App\DTO\ProjectDto;
use App\Models\Project;

class ProjectService
{
    public function getProjects(): array
    {
        return ProjectDto::fromCollection(
            Project::query()->get()
        );
    }
}
