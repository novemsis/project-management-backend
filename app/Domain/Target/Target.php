<?php

namespace App\Domain\Target;

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
 * @property string $definition
 * @property bool $smarter_spezifisch
 * @property bool $smarter_messbar
 * @property bool $smarter_ambitioniert
 * @property bool $smarter_realistisch
 * @property bool $smarter_terminiert
 * @property bool $smarter_emotionalisiert
 * @property bool $smarter_ressourceneinsatz
 * @property Carbon $created_at
 */
#[Table('targets')]
#[Fillable([
    'project_id',
    'definition',
    'smarter_spezifisch',
    'smarter_messbar',
    'smarter_ambitioniert',
    'smarter_realistisch',
    'smarter_terminiert',
    'smarter_emotionalisiert',
    'smarter_ressourceneinsatz',
])]
class Target extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'smarter_spezifisch' => 'boolean',
            'smarter_messbar' => 'boolean',
            'smarter_ambitioniert' => 'boolean',
            'smarter_realistisch' => 'boolean',
            'smarter_terminiert' => 'boolean',
            'smarter_emotionalisiert' => 'boolean',
            'smarter_ressourceneinsatz' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function getProject(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
