<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\FoundationRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Mail\FoundationPendingMail;
use App\Mail\NewFoundationRequestMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
                Rule::unique('foundation_requests', 'email')->whereNull('deleted_at')
            ],
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        try {
            // Create without transaction first
            $foundation = FoundationRequest::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'status' => 'pending',
            ]);

            // Send emails synchronously (important for debugging)
            Mail::to($request->email)->send(new FoundationPendingMail($foundation));

            // Send to admin - with failover
            try {
                Mail::to(config('mail.admin_address'))->send(new NewFoundationRequestMail($foundation));
            } catch (\Exception $e) {
                Log::error('Admin email failed: ' . $e->getMessage());
            }

            return redirect('/login')->with('status', 'Pendaftaran berhasil! Cek email Anda.');
        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Pendaftaran gagal. Error: ' . $e->getMessage()]);
        }
    }
}
