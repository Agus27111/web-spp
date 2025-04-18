<?php

namespace App\Filament\Admin\Resources\FoundationResource\Pages;

use App\Filament\Admin\Resources\FoundationResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFoundation extends CreateRecord
{
    protected static string $resource = FoundationResource::class;

    protected function afterCreate(): void
    {
        // Cek apakah foundation baru ada
        if ($this->record) {
            $user = User::create([
                'name' => $this->record->name,
                'email' => strtolower(str_replace(' ', '', $this->record->name)) . '@gmail.com',
                'password' => bcrypt('password123'), // password default
                'role' => 'foundation',
                'foundation_id' => $this->record->id,
                'email_verified_at' => now(),
            ]);

            $user->assignRole('foundation'); 

            // Update foundation dengan user_id (kalau kamu pakai user_id di foundations table)
            $this->record->update([
                'user_id' => $user->id,
            ]);
        }
    }
}
