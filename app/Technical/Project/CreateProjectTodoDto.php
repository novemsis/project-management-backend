<?php

namespace App\Technical\Project;

use Spatie\LaravelData\Data;

class CreateProjectTodoDto extends Data
{
    public function __construct(public string $description)
    {
    }
}
