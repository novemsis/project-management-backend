<?php

namespace App\Domain\Project;

use App\Domain\User\User;
use Illuminate\Database\Eloquent\Collection;

class ProjectService
{
    /** @return Collection<int, Project> */
    public function getProjects(User $user): Collection
    {
        return Project::query()->where('user_id', $user->id)->get();
    }

    public function getProject(User $user, String $projectId): ?Project
    {
        return Project::query()->where('user_id', $user->id)->where('id', $projectId)->first();
    }
}
