<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FoundationResource\Pages;
use App\Filament\Admin\Resources\FoundationResource\RelationManagers;
use App\Models\Foundation;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class FoundationResource extends Resource
{
    protected static ?string $model = Foundation::class;

    protected static ?string $navigationLabel = 'Yayasan';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('address')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->columnSpanFull(),
                Select::make('user_id')
                    ->relationship(
                        name: 'users',
                        titleAttribute: 'name',
                    )
                    ->createOptionForm([
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
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->numeric()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
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
                //
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
            'index' => Pages\ListFoundations::route('/'),
            'create' => Pages\CreateFoundation::route('/create'),
            'edit' => Pages\EditFoundation::route('/{record}/edit'),
        ];
    }
}
