<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Mail\FoundationApprovedMail;
use App\Mail\FoundationPendingMail;
use App\Mail\NewFoundationRequestMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

//Route::get('/admin', function () {
//return view('admin');
//})->middleware(['auth', 'verified'])->name('admin');

// Email test route
Route::get('/test-email', function () {
    Log::channel('emails')->info('Starting email test');

    $foundation = (object) [
        'name' => 'Test Foundation',
        'email' => 'agussetiawanphy3@gmail.com',
        'phone_number' => '123456789',
        'address' => 'Test Address',
        'status' => 'pending'
    ];

    try {
        Mail::to($foundation->email)->send(new FoundationPendingMail($foundation));
        Log::channel('emails')->info('FoundationPendingMail dispatched');

        $adminEmail = config('mail.admin_address');
        Mail::to($adminEmail)->send(new NewFoundationRequestMail($foundation));
        Log::channel('emails')->info('NewFoundationRequestMail dispatched');

        $password = 'testpassword';
        Mail::to($foundation->email)->send(new FoundationApprovedMail($foundation, $password));
        Log::channel('emails')->info('FoundationApprovedMail dispatched');

        return response()->json([
            'status' => 'success',
            'message' => 'All email dispatches successful'
        ]);
    } catch (\Exception $e) {
        Log::channel('emails')->error('Email test failed', [
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});
require __DIR__ . '/auth.php';
