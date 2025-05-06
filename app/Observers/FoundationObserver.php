<?php

namespace App\Observers;

use App\Models\Foundation;
use App\Models\User;
use Illuminate\Support\Str;

class FoundationObserver
{
    /**
     * Handle the Foundation "created" event.
     */
    public function created(Foundation $foundation): void
    {
        User::create([
            'name' => $foundation->name,
            'email' => strtolower(Str::slug($foundation->name)) . '@yayasan.com',
            'password' => bcrypt('12345678'),
            'role' => 'foundation',
            'foundation_id' => $foundation->id,
        ]);
    }

    /**
     * Handle the Foundation "updated" event.
     */
    public function updated(Foundation $foundation): void
    {
        //
    }

    /**
     * Handle the Foundation "deleted" event.
     */
    public function deleted(Foundation $foundation): void
    {
        //
    }

    /**
     * Handle the Foundation "restored" event.
     */
    public function restored(Foundation $foundation): void
    {
        //
    }

    /**
     * Handle the Foundation "force deleted" event.
     */
    public function forceDeleted(Foundation $foundation): void
    {
        //
    }
}
