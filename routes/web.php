<?php

use App\Http\Middleware\CheckProjectAccess;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('home');
});

Auth::routes();

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('projects/generate/{quantity}/{year}', [ProjectController::class, 'generate'])->name('projects.generate');

    Route::post('usermanager/adduser', [UserController::class, 'adduser'])->name('usermanager.adduser');
    Route::post('usermanager/addcompany', [UserController::class, 'addcompany'])->name('usermanager.addcompany');

    Route::get('usermanager', [UserController::class, 'index'])->name('usermanager');
    Route::get('profile', [ProfileController::class, 'index'])->name('profile');

    Route::get('projects/project/{id}', [ProjectController::class, 'index'])->middleware(CheckProjectAccess::class)->name('projects.project');
});

