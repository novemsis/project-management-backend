<?php

namespace App\Domain;

use App\Domain\Project\Project;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
