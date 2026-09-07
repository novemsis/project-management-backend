<?php

namespace App\Domain\Plan;

use App\Domain\Project\Project;

class PlanService
{
    public function createPlan(
        Project $project,
        ?string $strategicDefinition,
        ?string $tacticalDefinition,
        ?string $operationalDefinition
    ): void {
        $strategicDefinition = $this->getStrategicDefinition($strategicDefinition);
        $tacticalDefinition = $this->getTacticalDefinition($tacticalDefinition);
        $operationalDefinition = $this->getOperationalDefinition($operationalDefinition);
        if (
            null !== $strategicDefinition
            || null !== $tacticalDefinition
            || null !== $operationalDefinition
        ) {
            $plan = new Plan([
                'project_id' => $project->id,
                'strategic_definition' => $strategicDefinition,
                'tactical_definition' => $tacticalDefinition,
                'operational_definition' => $operationalDefinition
            ]);
            $plan->save();
        }
    }

    private function getStrategicDefinition(?string $strategicDefinition): ?string
    {
        if (null === $strategicDefinition) {
            return null;
        }

        return empty(trim($strategicDefinition)) ? null : $strategicDefinition;
    }

    private function getTacticalDefinition(?string $tacticalDefinition): ?string
    {
        if (null === $tacticalDefinition) {
            return null;
        }

        return empty(trim($tacticalDefinition)) ? null : $tacticalDefinition;
    }

    private function getOperationalDefinition(?string $operationalDefinition): ?string
    {
        if (null === $operationalDefinition) {
            return null;
        }

        return empty(trim($operationalDefinition)) ? null : $operationalDefinition;
    }

    public function isPlanDefined(Plan $currentPlan): bool
    {
        return null !== $currentPlan->strategic_definition
            && null !== $currentPlan->tactical_definition
            && null !== $currentPlan->operational_definition;
    }
}
