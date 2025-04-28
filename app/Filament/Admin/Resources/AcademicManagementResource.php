<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AcademicManagementResource\Pages;
use App\Models\AcademicYear;
use App\Models\Foundation;
use App\Models\Unit;
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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //         ->withoutGlobalScopes([SoftDeletingScope::class])
    //         ->where('foundation_id', Auth::user()->foundation_id)
    //         ->withCount(['units', 'classrooms']);
    // }



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
                                Forms\Components\Select::make('is_active')
                                    ->label('Status')
                                    ->options([
                                        true => '✅ Aktif',
                                        false => '❌ Tidak Aktif',
                                    ])
                                    ->required(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Unit Lembaga')
                            ->schema([
                                Forms\Components\Select::make('units')
                                    ->label('Pilih Unit')
                                    ->multiple()
                                    ->relationship('units', 'name')
                                    ->preload()
                                    ->searchable()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nama Unit')
                                            ->required(),
                                    ]),

                                Forms\Components\Select::make('foundation_id')
                                    ->label('Foundation')
                                    ->options(Foundation::forUser()->pluck('name', 'id'))
                                    ->required()
                                    ->visible(fn() => Auth::user()->role === 'superadmin'),
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
                                            ->options(Unit::pluck('name', 'id'))
                                            ->searchable()
                                            ->required(),
                                    ])
                            ]),
                    ])
                    ->columnSpanFull()
                    ->contained(),
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
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
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
            'index' => Pages\ListAcademicManagement::route('/'),
            'create' => Pages\CreateAcademicManagement::route('/create'),
            'edit' => Pages\EditAcademicManagement::route('/{record}/edit'),
        ];
    }
}
