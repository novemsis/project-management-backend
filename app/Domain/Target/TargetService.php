<?php

namespace App\Domain\Target;

use App\Domain\Project\Project;

class TargetService
{
    public function createTarget(Project $project, ?string $targetDefinition): void
    {
        if ($this->isEmpty($targetDefinition)) {
            return;
        }

        $target = new Target([
            'project_id' => $project->id,
            'definition' => $targetDefinition,
            'smarter_spezifisch' => false,
            'smarter_messbar' => false,
            'smarter_ambitioniert' => false,
            'smarter_realistisch' => false,
            'smarter_terminiert' => false,
            'smarter_emotionalisiert' => false,
            'smarter_ressourceneinsatz' => false
        ]);
        $target->save();
    }

    private function isEmpty(?string $definition): bool
    {
        return null === $definition || empty(trim($definition));
    }

    public function isTargetSmarter(Target $target): bool
    {
        return $target->smarter_spezifisch
            && $target->smarter_messbar
            && $target->smarter_ambitioniert
            && $target->smarter_realistisch
            && $target->smarter_terminiert
            && $target->smarter_emotionalisiert
            && $target->smarter_ressourceneinsatz;
    }
}
