<?php

namespace App\Domain\Project;

use App\Domain\Decision;
use App\Domain\PerformedCheck;
use App\Domain\Plan;
use App\Domain\Target;
use App\Domain\ToDo;
use App\Domain\User\User;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Table('projects')]
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
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function latestPerformedCheck(): HasOne
    {
        return $this->hasOne(PerformedCheck::class)
            ->latestOfMany('created_at');
    }

    public function targets(): HasMany {
        return $this->hasMany(Target::class);
    }

    public function currentTarget(): ?Target
    {
        return $this->latestPerformedCheck?->target
            ?? $this->targets()->first();
    }

    public function plans(): HasMany {
        return $this->hasMany(Plan::class);
    }

    public function currentPlan(): ?Plan
    {
        return $this->latestPerformedCheck?->plan
            ?? $this->plans()->first();
    }

    public function decisions(): HasMany {
        return $this->hasMany(Decision::class);
    }

    public function currentDecision(): ?Decision
    {
        return $this->latestPerformedCheck?->decision
            ?? $this->decisions()->first();
    }

    public function toDos(): HasMany {
        return $this->hasMany(ToDo::class);
    }

    public function currentToDos()
    {
        return $this->latestPerformedCheck?->toDos
            ?? $this->toDos;
    }
}
