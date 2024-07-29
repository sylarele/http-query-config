<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Workbench\App\Http\Controllers\FooController;

Route::get('foos', [FooController::class, 'index'])->name('foos.index');
