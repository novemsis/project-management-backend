<?php

use App\Technical\Project\ProjectController;
use Illuminate\Support\Facades\Route;

Route::post('/project/create', [ProjectController::class, 'createProject']);
