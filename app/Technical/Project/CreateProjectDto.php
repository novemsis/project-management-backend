<?php

namespace App\Technical\Project;

use App\Global\DatePeriod;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class CreateProjectDto extends Data
{
    /** @param CreateProjectTodoDto[] $toDos */
    public function __construct(
        public string $title,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public ?Carbon $projectStart = null,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public ?Carbon $projectEnd = null,
        public ?DatePeriod $checkPeriod = null,
        public ?string $targetDefinition = null,
        public ?string $planStrategicDefinition = null,
        public ?string $planTacticalDefinition = null,
        public ?string $planOperationalDefinition = null,
        public ?bool $decisionCarryThrough = null,
        public ?string $decisionDescription = null,
        /** @var CreateProjectTodoDto[] */
        public array $toDos = []
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getProjectStart(): ?Carbon
    {
        return $this->projectStart;
    }

    public function getProjectEnd(): ?Carbon
    {
        return $this->projectEnd;
    }

    public function getCheckPeriod(): ?DatePeriod
    {
        return $this->checkPeriod;
    }

    public function getTargetDefinition(): ?string
    {
        return $this->targetDefinition;
    }

    public function getPlanStrategicDefinition(): ?string
    {
        return $this->planStrategicDefinition;
    }

    public function getPlanTacticalDefinition(): ?string
    {
        return $this->planTacticalDefinition;
    }

    public function getPlanOperationalDefinition(): ?string
    {
        return $this->planOperationalDefinition;
    }

    public function getDecisionCarryThrough(): ?bool
    {
        return $this->decisionCarryThrough;
    }

    public function getDecisionDescription(): ?string
    {
        return $this->decisionDescription;
    }

    /** @return CreateProjectTodoDto[] */
    public function getToDos(): array
    {
        return $this->toDos;
    }
}
