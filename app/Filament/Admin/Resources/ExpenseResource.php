<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;
    protected static ?string $navigationLabel = 'Pengeluaran';
    protected static ?string $modelLabel = 'pengeluaran';
    protected static ?string $pluralModelLabel = 'daftar pengeluaran';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-circle';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['foundation']); // Eager loading foundation

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
                    ->label('Nama Pengeluaran')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                
                Forms\Components\Select::make('category')
                    ->label('Kategori')
                    ->options([
                        'gaji' => 'Gaji & Tunjangan',
                        'operasional' => 'Biaya Operasional',
                        'pemeliharaan' => 'Pemeliharaan',
                        'lainnya' => 'Lainnya'
                    ])
                    ->required(),
                
                Forms\Components\DatePicker::make('date')
                    ->label('Tanggal')
                    ->required()
                    ->default(now()),
                
                Forms\Components\Textarea::make('description')
                    ->label('Keterangan')
                    ->columnSpanFull(),
                
                Forms\Components\FileUpload::make('payment_proof')
                    ->label('Bukti Pembayaran')
                    ->directory('expense-proofs')
                    ->image()
                    ->maxSize(2048) // 2MB
                    ->downloadable()
                    ->openable()
                    ->columnSpanFull(),
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
                    ->label('Nama Pengeluaran')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->numeric()
                    ->prefix('Rp')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->formatStateUsing(fn($state) => match($state) {
                        'gaji' => 'Gaji',
                        'operasional' => 'Operasional',
                        'pemeliharaan' => 'Pemeliharaan',
                        'lainnya' => 'Lainnya',
                        default => $state
                    }),
                
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\ImageColumn::make('payment_proof')
                    ->label('Bukti')
                    ->size(40),
                
                Tables\Columns\TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'gaji' => 'Gaji & Tunjangan',
                        'operasional' => 'Biaya Operasional',
                        'pemeliharaan' => 'Pemeliharaan',
                        'lainnya' => 'Lainnya'
                    ]),
                
                Tables\Filters\SelectFilter::make('foundation_id')
                    ->label('Yayasan')
                    ->relationship('foundation', 'name')
                    ->visible(fn() => Auth::user()->hasRole('superadmin')),
                
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}