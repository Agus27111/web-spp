<?php

namespace App\Filament\Admin\Resources;

use Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource\Pages\CreateRole;
use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Models\Foundation;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $user = auth()->user();

        $foundationField = $user && $user->hasRole('superadmin')
            ? Forms\Components\Select::make('foundation_id')
            ->relationship('foundation', 'name')
            ->label('Foundation')
            ->required()
            : Forms\Components\Hidden::make('foundation_id')
            ->default($user->foundation_id);

        return $form
            ->schema([
                $foundationField,

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn(string $state): string => Hash::make($state))
                    ->dehydrated(fn(?string $state): bool => filled($state))
                    ->required(fn($livewire): bool => $livewire instanceof CreateRecord),

                Forms\Components\DatePicker::make('email_verified_at')
                    ->default(now())
                    ->label('Email Verified At'),

                Forms\Components\Select::make('role')
                    ->relationship('roles', 'name')
                    ->required(),

                Forms\Components\TextInput::make('phone_number')
                    ->tel()
                    ->maxLength(255),

                Forms\Components\FileUpload::make('image')
                    ->image(),


            ]);
    }


    public static function table(Table $table): Table
    {
        $authUser = Auth::user();
        $query = User::query();
        if (!$authUser->hasRole('superadmin')) {
            $query->where('role', '!=', 'superadmin');
        }

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('foundation_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role'),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('foundation_id')
                    ->options(function () use ($authUser) {
                        return $authUser->hasRole('foundation')
                            ? [$authUser->foundation_id => 'Foundation ' . $authUser->foundation_id]
                            : [];
                    })
                    ->default($authUser->foundation_id ?? null),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
