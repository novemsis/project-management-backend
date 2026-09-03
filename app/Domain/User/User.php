<?php

namespace App\Domain\User;

use App\Domain\Project\Project;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('users')]
#[Fillable([
    'username',
    'first_name',
    'last_name',
])]
class User extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    const UPDATED_AT = null;

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    /* @return HasMany<Project> */
    public function getProjects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
