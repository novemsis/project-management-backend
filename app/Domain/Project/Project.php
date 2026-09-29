<?php

namespace App\Domain\Project;

use App\Domain\Check\PerformedCheck;
use App\Domain\Decision\Decision;
use App\Domain\Plan\Plan;
use App\Domain\Target\Target;
use App\Domain\ToDo\ToDo;
use App\Domain\User\User;
use App\Global\DatePeriod;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $title
 * @property string|null $description
 * @property Carbon|null $project_start
 * @property Carbon|null $project_end
 * @property DatePeriod|null $check_period
 * @property Carbon $created_at
 */
#[Table('projects')]
#[Fillable([
    'user_id',
    'title',
    'description',
    'project_start',
    'project_end',
    'check_period',
])]
class Project extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected function casts(): array
    {
        return [
            'project_start' => 'datetime',
            'project_end' => 'datetime',
            'check_period' => DatePeriod::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function performedChecks(): HasMany
    {
        return $this->hasMany(PerformedCheck::class);
    }


    public function latestPerformedCheck(): ?PerformedCheck
    {
        return $this->performedChecks()->latest()->first();
    }

    public function targets(): HasMany {
        return $this->hasMany(Target::class);
    }

    public function currentTarget(): ?Target
    {
        return $this->latestPerformedCheck()?->target
            ?? $this->targets()->latest()->first();
    }

    public function plans(): HasMany {
        return $this->hasMany(Plan::class);
    }

    public function currentPlan(): ?Plan
    {
        return $this->latestPerformedCheck()?->plan
            ?? $this->plans()->latest()->first();
    }

    public function decisions(): HasMany {
        return $this->hasMany(Decision::class);
    }

    public function currentDecision(): ?Decision
    {
        return $this->latestPerformedCheck()?->decision
            ?? $this->decisions()->latest()->first();
    }

    public function toDos(): HasMany {
        return $this->hasMany(ToDo::class);
    }

    /** @return Collection<int, ToDo> */
    public function currentToDos(): Collection
    {
        return $this->latestPerformedCheck()?->toDos
            ?? $this->toDos()->get();
    }
}
