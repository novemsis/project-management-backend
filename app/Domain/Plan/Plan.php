<?php

namespace App\Domain\Plan;

use App\Domain\Project\Project;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;

/**
 * @property string $id
 * @property Carbon $created_at
 * @property string|null $strategic_definition
 * @property string|null $tactical_definition
 * @property string|null $operational_definition
 */
#[Table('plans')]
#[Fillable([
    'project_id',
    'strategic_definition',
    'tactical_definition',
    'operational_definition',
])]
class Plan extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
