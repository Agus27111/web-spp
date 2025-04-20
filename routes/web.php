<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

//Route::get('/admin', function () {
//return view('admin');
//})->middleware(['auth', 'verified'])->name('admin');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/test-email', function () {
    $record = (object) [
        'name' => 'Test Foundation',
        'email' => 'agussetiawanphy3@gmail.com',
        'phone_number' => '123456789',
        'address' => 'Test Address',
    ];
    $password = 'testpassword';
    Mail::to($record->email)->send(new \App\Mail\FoundationApprovedMail($record, $password));
    return 'Email test terkirim';
});

require __DIR__ . '/auth.php';
