<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AcademicManagementResource\Pages;
use App\Filament\Admin\Resources\AcademicManagementResource\RelationManagers;
use App\Models\AcademicManagement;
use App\Models\AcademicYear;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AcademicManagementResource extends Resource
{
    protected static ?string $model = AcademicYear::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->withCount(['units', 'classrooms']);
    }

    protected static ?string $navigationLabel = 'Tahun Ajaran';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Academic Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Tahun Akademik')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Toggle::make('is_activ')
                                    ->label('Aktif?')
                                    ->required(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Unit Lembaga')
                            ->schema([
                                Forms\Components\Repeater::make('units')
                                    ->relationship('units')
                                    ->schema([
                                        Forms\Components\Select::make('name')
                                            ->label('Nama Unit')
                                            ->options([
                                                'kober' => 'Kober',
                                                'tk' => 'TK',
                                                'sd' => 'SD',
                                                'smp' => 'SMP',
                                                'sma' => 'SMA',
                                                'universitas' => 'Universitas',
                                            ])
                                            ->required(),
                                    ])
                                    ->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Kelas')
                            ->schema([
                                Forms\Components\Repeater::make('classrooms')
                                    ->relationship('classrooms')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nama Kelas')
                                            ->required(),

                                        Forms\Components\Select::make('unit_id')
                                            ->label('Unit')
                                            ->relationship('unit', 'name')
                                            ->required(),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tahun Akademik')
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_activ')
                    ->boolean()
                    ->label('Aktif'),

                Tables\Columns\TextColumn::make('units_count')
                    ->counts('units')
                    ->label('Jumlah Unit'),

                Tables\Columns\TextColumn::make('classrooms_count')
                    ->counts('classrooms')
                    ->label('Jumlah Kelas'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Dibuat'),
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
            'index' => Pages\ListAcademicManagement::route('/'),
            'create' => Pages\CreateAcademicManagement::route('/create'),
            'edit' => Pages\EditAcademicManagement::route('/{record}/edit'),
        ];
    }
}
