<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FeeTypeResource\Pages;
use App\Models\FeeType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class FeeTypeResource extends Resource
{
    protected static ?string $model = FeeType::class;
    protected static ?string $navigationLabel = 'Tipe Pembayaran';
    protected static ?string $modelLabel = 'tipe pembayaran';
    protected static ?string $pluralModelLabel = 'daftar tipe pembayaran';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['foundation']); // Eager loading

        return Auth::user()->hasRole('superadmin')
            ? $query
            : $query->where('foundation_id', Auth::user()->foundation_id);
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
                    ->required()
                    ->visible(fn() => Auth::user()->hasRole('superadmin'))
                    ->default(fn() => Auth::user()->foundation_id),

                // Untuk non-superadmin - set otomatis
                Forms\Components\Hidden::make('foundation_id')
                    ->default(fn() => Auth::user()->foundation_id)
                    ->visible(fn() => !Auth::user()->hasRole('superadmin')),

                Forms\Components\Select::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->relationship('academicYear', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                    ]),

                Forms\Components\TextInput::make('name')
                    ->label('Nama Tipe Pembayaran')
                    ->datalist([
                        'SPP',
                        'Uang Gedung',
                        'Seragam',
                        'Kegiatan',
                    ])
                    ->required(),

                Forms\Components\Select::make('frequency')
                    ->label('Frekuensi')
                    ->required()
                    ->default('monthly')
                    ->options([
                        'monthly' => 'Bulanan',
                        'semester' => 'Per Semester',
                        'yearly' => 'Tahunan',
                        'once' => 'Sekali Bayar'
                    ]),

                Forms\Components\Repeater::make('fees')
                    ->relationship()
                    ->label('Detail Biaya')
                    ->schema([

                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah')
                            ->required()
                            ->numeric()
                            ->prefix('Rp'),
                    ])
                    ->columns(2)
                    ->defaultItems(1)
                    ->collapsible()
                    ->itemLabel(fn(array $state): ?string => $state['name'] ?? null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('foundation.name')
                    ->label('Yayasan')
                    ->toggleable()
                    ->visible(fn() => Auth::user()->hasRole('superadmin')),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Tipe')
                    ->searchable(),

                Tables\Columns\TextColumn::make('frequency')
                    ->label('Frekuensi')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'monthly' => 'Bulanan',
                        'semester' => 'Per Semester',
                        'yearly' => 'Tahunan',
                        'once' => 'Sekali Bayar',
                        default => $state
                    }),

                Tables\Columns\TextColumn::make('fees_sum_amount')
                    ->label('Total Biaya')
                    ->numeric()
                    ->prefix('Rp')
                    ->sortable(query: function (Builder $query, string $direction) {
                        $query->withSum('fees', 'amount')->orderBy('fees_sum_amount', $direction);
                    })
                    ->sum('fees', 'amount'), // Add this instead of using withSum in query

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('frequency')
                    ->label('Frekuensi')
                    ->options([
                        'monthly' => 'Bulanan',
                        'semester' => 'Per Semester',
                        'yearly' => 'Tahunan',
                        'once' => 'Sekali Bayar'
                    ]),

                Tables\Filters\SelectFilter::make('foundation_id')
                    ->label('Yayasan')
                    ->relationship('foundation', 'name')
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeeTypes::route('/'),
            'create' => Pages\CreateFeeType::route('/create'),
            'edit' => Pages\EditFeeType::route('/{record}/edit'),
        ];
    }
}
