<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\IncomeResource\Pages;
use App\Models\Income;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class IncomeResource extends Resource
{
    protected static ?string $model = Income::class;
    protected static ?string $navigationLabel = 'Pemasukan';
    protected static ?string $modelLabel = 'pemasukan';
    protected static ?string $pluralModelLabel = 'daftar pemasukan';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-circle';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['academicYear.foundation']); // Eager loading

        return Auth::user()->hasRole('superadmin')
            ? $query
            : $query->whereHas('academicYear', function ($q) {
                $q->where('foundation_id', Auth::user()->foundation_id);
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Untuk superadmin - pilih yayasan
                Forms\Components\Select::make('foundation_id')
                    ->label('Yayasan')
                    ->relationship('foundation', 'name')
                    ->searchable()
                    ->visible(fn() => Auth::user()->hasRole('superadmin'))
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        // Reset academic year ketika yayasan berubah
                        $set('academic_year_id', null);
                    }),

                Forms\Components\Select::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->relationship(
                        name: 'academicYear',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query, callable $get) =>
                        Auth::user()->hasRole('superadmin')
                            ? $query->where('foundation_id', $get('foundation_id'))
                            : $query->where('foundation_id', Auth::user()->foundation_id)
                    )
                    ->required()
                    ->native(false),

                Forms\Components\TextInput::make('source')
                    ->label('Sumber Pemasukan')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),

                Forms\Components\Textarea::make('description')
                    ->label('Keterangan')
                    ->columnSpanFull(),

                Forms\Components\DatePicker::make('date')
                    ->label('Tanggal')
                    ->required()
                    ->default(now()),

                // Hidden field untuk non-superadmin
                Forms\Components\Hidden::make('foundation_id')
                    ->default(fn() => Auth::user()->foundation_id)
                    ->visible(fn() => !Auth::user()->hasRole('superadmin')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('Tahun Ajaran')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('academicYear.foundation.name')
                    ->label('Yayasan')
                    ->toggleable()
                    ->visible(fn() => Auth::user()->hasRole('superadmin')),

                Tables\Columns\TextColumn::make('source')
                    ->label('Sumber')
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->numeric()
                    ->prefix('Rp')
                    ->sortable(),

                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->relationship('academicYear', 'name')
                    ->searchable(),

                Tables\Filters\SelectFilter::make('foundation_id')
                    ->label('Yayasan')
                    ->relationship('academicYear.foundation', 'name')
                    ->visible(fn() => Auth::user()->hasRole('superadmin')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncomes::route('/'),
            'create' => Pages\CreateIncome::route('/create'),
            'edit' => Pages\EditIncome::route('/{record}/edit'),
        ];
    }
}
