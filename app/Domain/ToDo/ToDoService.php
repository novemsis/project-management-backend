<?php

namespace App\Domain\ToDo;

use App\Domain\Project\Project;

class ToDoService
{
    /** @param array<int, string> $toDos */
    public function createToDosFromArray(Project $project, array $toDos): void
    {
        foreach ($toDos as $description) {
            if (!empty(trim($description))) {
                $toDo = new ToDo(['project_id' => $project->id, 'description' => $description, 'done' => false]);
                $project->toDos()->save($toDo);
            }
        }
    }
}
