<?php

namespace App\Domain\User;

use App\Domain\Project\Project;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use Ramsey\Uuid\Uuid;

/** @property string $id */
#[Table('users')]
#[Fillable([
    'username',
    'first_name',
    'last_name',
    'password'
])]
#[Hidden([
    'password',
    'remember_token',
])]
class User extends Authenticatable
{
    use HasUuids;
    use HasApiTokens;

    public $incrementing = false;

    protected $keyType = 'string';

    const ?string UPDATED_AT = null;

    protected function casts(): array
    {
        return ['password' => 'hashed', 'created_at' => 'datetime'];
    }

    /* @return HasMany<Project> */
    public function getProjects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function checkPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
}
