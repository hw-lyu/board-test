<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::resource('post', PostController::class)->except(['create', 'show']);


