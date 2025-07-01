<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AcademicManagementResource\Pages;
use App\Filament\Admin\Resources\AcademicManagementResource\RelationManagers\ClassroomsRelationManager;
use App\Filament\Admin\Resources\AcademicManagementResource\RelationManagers\UnitsRelationManager;
use App\Models\AcademicYear;
use App\Models\Foundation;
use App\Models\Unit;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class AcademicManagementResource extends Resource
{
    protected static ?string $model = AcademicYear::class;

    protected static ?string $navigationLabel = 'Tahun Ajaran';

    protected static ?string $modelLabel = 'tahun ajaran';

    protected static ?string $pluralModelLabel = 'daftar tahun ajaran';

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $titleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('foundation_id')
                            ->label('Foundation')
                            ->options(Foundation::query()->pluck('name', 'id'))
                            ->required()
                            ->visible(fn() => Auth::user()->role === 'superadmin')
                            ->default(fn() => Auth::user()->foundation_id)
                            ->disabled(fn() => Auth::user()->role !== 'superadmin')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Yayasan')
                                    ->required(),
                                Forms\Components\TextInput::make('phone_number')
                                    ->label('Nomor Telepon')
                                    ->tel()
                                    ->required(),
                            ])
                            ->createOptionUsing(function (array $data) {
                                return Foundation::create([
                                    'name' => $data['name'],
                                    'phone_number' => $data['phone_number'],
                                ]);
                            }),

                        Forms\Components\TextInput::make('name')
                            ->label('Nama Tahun Akademik')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('is_active')
                            ->label('Status')
                            ->options([
                                true => '✅ Aktif',
                                false => '❌ Tidak Aktif',
                            ])
                            ->required(),
                    ])
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->query(
                AcademicYear::query()
                    ->withCount('units', 'classrooms')
            )
            ->columns([
                Tables\Columns\TextColumn::make('foundation.name')
                    ->label('Foundation')
                    ->visible(fn() => Auth::user()->role === 'superadmin')
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Tahun Akademik')
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Aktif')
                    ->trueIcon('heroicon-m-check-circle')
                    ->falseIcon('heroicon-m-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('units_count')
                    ->label('Jumlah Unit'),

                Tables\Columns\TextColumn::make('classrooms_count')
                    ->label('Jumlah Kelas'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            UnitsRelationManager::class,
            ClassroomsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAcademicManagement::route('/'),
            'create' => Pages\CreateAcademicManagement::route('/create'),
            'edit' => Pages\EditAcademicManagement::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                // Add any scopes you don't want here
            ]);
    }
}
