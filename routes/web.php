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

    //
    // PROJECTS
    //
    Route::get('projects/generate/{quantity}/{year}', [ProjectController::class, 'generate'])->name('projects.generate');
    Route::get('projects/delete/{project}', [ProjectController::class, 'delete'])->name('projects.delete');
    Route::get('projects/project/{id}', [ProjectController::class, 'index'])->middleware(CheckProjectAccess::class)->name('projects.project');
    Route::post('projects/delete-selected', [ProjectController::class, 'deleteSelected'])->name('projects.delete-selected');
    Route::post('projects/delete-empty', [ProjectController::class, 'deleteEmptyProject'])->name('projects.delete-empty');
    Route::post('projects/add', [ProjectController::class, 'store'])->name('projects.store');
    Route::post('projects/upload/{project}', [ProjectController::class, 'uploadFiles'])->name('projects.upload');

    //
    // PROFILE
    //
    Route::get('profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('profile/update', [ProfileController::class, 'update'])->name('profile.update');
    //
    // USER MANAGER
    //
    Route::get('usermanager', [UserController::class, 'index'])->name('usermanager');
    //USER
    Route::post('usermanager/adduser', [UserController::class, 'adduser'])->name('usermanager.adduser');
    Route::post('usermanager/addcompany', [UserController::class, 'addcompany'])->name('usermanager.addcompany');
    // ENTREPRISES
    Route::post('usermanager/updatecompany', [UserController::class, 'updateCompany'])->name('usermanager.updatecompany');
    Route::post('usermanager/deletecompany', [UserController::class, 'deleteCompany'])->name('usermanager.deletecompany');
    // UTILISATEURS
    Route::post('usermanager/updateuser', [UserController::class, 'updateUser'])->name('usermanager.updateuser');
    Route::post('usermanager/deleteuser', [UserController::class, 'deleteUser'])->name('usermanager.deleteuser');
});

