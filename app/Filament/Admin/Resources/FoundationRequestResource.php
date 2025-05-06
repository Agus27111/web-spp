<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FoundationRequestResource\Pages;
use App\Filament\Admin\Resources\FoundationRequestResource\RelationManagers;
use App\Models\Foundation;
use App\Models\FoundationRequest;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Mail;
use App\Mail\FoundationApprovedMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class FoundationRequestResource extends Resource
{
    protected static ?string $model = FoundationRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->maxLength(255),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255)
                    ->default('pending'),
                Forms\Components\DateTimePicker::make('email_verified_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        // Cek apakah user sudah ada
                        $existingUser = User::where('email', $record->email)->first();
                        if ($existingUser) {
                            return;
                        }

                        try {
                            // Generate password
                            $password = generateFriendlyPassword(8);

                            // dd($password, $record);

                            DB::beginTransaction();

                            // Buat user
                            $user = User::create([
                                'name' => $record->name,
                                'email' => $record->email,
                                'password' => bcrypt($password),
                                'role' => 'foundation',
                                'phone_number' => $record->phone_number,
                                'email_verified_at' => now(),
                            ]);

                            $user->assignRole('foundation');

                            // Buat Foundation
                            $foundation = Foundation::create([
                                'name' => $record->name,
                                'address' => $record->address,
                                'image' => $record->image ?? null,
                                'user_id' => $user->id,
                                'phone_number' => $record->phone_number,
                            ]);

                            $user->update([
                                'foundation_id' => $foundation->id,
                            ]);

                            if (!$record->email) {
                                throw new \Exception("Email kosong. Data record: " . json_encode($record));
                            }
                            // Kirim email
                            try {
                                Log::info("Mengirim email ke: {$record->email}");
                                Mail::to($record->email)->send(new FoundationApprovedMail($record, $password));
                                Log::info("Email berhasil dikirim ke: {$record->email}");
                            } catch (\Exception $e) {
                                DB::rollBack();
                                Log::error("Gagal kirim email ke {$record->email}: " . $e->getMessage());
                                throw new \Exception("Email gagal dikirim: " . $e->getMessage());
                            }

                            // Update status ke approved
                            $record->update([
                                'status' => 'approved',
                            ]);


                            DB::commit();
                        } catch (\Exception $e) {
                            DB::rollBack();
                            Log::error("Terjadi kesalahan saat approve: " . $e->getMessage());
                            throw new \Exception("Terjadi kesalahan saat approve: " . $e->getMessage());
                        }
                    }),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFoundationRequests::route('/'),
            'create' => Pages\CreateFoundationRequest::route('/create'),
            'edit' => Pages\EditFoundationRequest::route('/{record}/edit'),
        ];
    }
}
