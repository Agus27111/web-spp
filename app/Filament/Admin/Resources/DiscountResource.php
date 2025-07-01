<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DiscountResource\Pages;
use App\Models\Discount;
use App\Models\FeeType;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DiscountResource extends Resource
{
    protected static ?string $model = Discount::class;
    protected static ?string $navigationLabel = 'Potongan';
    protected static ?string $modelLabel = 'potongan';
    protected static ?string $pluralModelLabel = 'daftar potongan';
    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['foundation', 'studentDiscounts.student', 'studentDiscounts.feeType']); // Eager loading 

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

                Forms\Components\TextInput::make('name')
                    ->label('Nama Potongan')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah Potongan')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),

                Forms\Components\Select::make('type')
                    ->label('Jenis Potongan')
                    ->options([
                        'percentage' => 'Persentase (%)',
                        'fixed' => 'Nominal Tetap',
                    ])
                    ->required()
                    ->default('fixed')
                    ->live(),

                Forms\Components\Textarea::make('description')
                    ->label('Keterangan')
                    ->columnSpanFull(),

                Forms\Components\Select::make('is_active')
                    ->label('Status')
                    ->options([
                        true => '✅ Aktif',
                        false => '❌ Tidak Aktif',
                    ])
                    ->required(),

                Forms\Components\Section::make('Penetapan Siswa')
                    ->schema([
                        Forms\Components\Repeater::make('studentDiscounts')
                            ->relationship(
                                name: 'studentDiscounts', // Must match the method name in Discount model
                                modifyQueryUsing: fn(Builder $query) =>
                                $query->where('foundation_id', Auth::user()->foundation_id)
                            )
                            ->label('Siswa yang Mendapat Potongan')
                            ->schema([
                                Forms\Components\Select::make('student_id')
                                    ->label('Siswa')
                                    ->relationship(
                                        name: 'student', // Must match StudentDiscount method
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn(Builder $query) =>
                                        $query->where('foundation_id', Auth::user()->foundation_id)
                                    )
                                    ->searchable()
                                    ->required(),

                                Forms\Components\Select::make('fee_type_id')
                                    ->label('Jenis Pembayaran')
                                    ->relationship(
                                        name: 'feeType', // Must match StudentDiscount method
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn(Builder $query) =>
                                        $query->where('foundation_id', Auth::user()->foundation_id)
                                    )
                                    ->searchable()
                                    ->required(),
                            ])


                            ->columns(2)
                            ->collapsible()
                            ->itemLabel(
                                fn(array $state): ?string =>
                                Student::find($state['student_id'])?->name . ' - ' .
                                    FeeType::find($state['fee_type_id'])?->name
                            )
                    ])
                    ->collapsed()
                    ->visible(fn($operation) => $operation === 'edit'),
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
                    ->label('Nama Potongan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->type === 'percentage'
                            ? $state . '%'
                            : 'Rp ' . number_format($state, 0, ',', '.');
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'percentage' => 'Persentase',
                        'fixed' => 'Nominal',
                        default => $state
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('student_discounts_count')
                    ->label('Siswa Terdaftar')
                    ->counts('studentDiscounts')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Jenis Potongan')
                    ->options([
                        'percentage' => 'Persentase',
                        'fixed' => 'Nominal',
                    ]),

                Tables\Filters\SelectFilter::make('foundation_id')
                    ->label('Yayasan')
                    ->relationship('foundation', 'name')
                    ->visible(fn() => Auth::user()->hasRole('superadmin')),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif')
                    ->queries(
                        true: fn(Builder $query) => $query->where('is_active', true),
                        false: fn(Builder $query) => $query->where('is_active', false),
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('manage_students')
                    ->label('Kelola Siswa')
                    ->url(fn(Discount $record): string => route('filament.admin.resources.discounts.students', $record))
                    ->icon('heroicon-o-users'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiscounts::route('/'),
            'create' => Pages\CreateDiscount::route('/create'),
            'edit' => Pages\EditDiscount::route('/{record}/edit'),
            'students' => Pages\ManageDiscountStudents::route('/{record}/students'),
        ];
    }
}
