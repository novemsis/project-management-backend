<?php

namespace App\Domain\Decision;

use App\Domain\Project\Project;

class DecisionService
{
    public function createDecision(Project $project, ?bool $carryThrough, ?string $decisionDescription): void
    {
        if (null === $carryThrough) {
            return;
        }

        if (null !== $decisionDescription && empty(trim($decisionDescription))) {
            $decisionDescription = null;
        }

        $decision = new Decision(['project_id' => $project->id, 'carry_through' => $carryThrough, 'description' => $decisionDescription]);
        $decision->save();
    }
}
