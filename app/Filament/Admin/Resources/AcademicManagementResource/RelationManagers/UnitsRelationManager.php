<?php

namespace App\Filament\Admin\Resources\AcademicManagementResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UnitsRelationManager extends RelationManager
{
    protected static string $relationship = 'units';

    // Label untuk judul relation manager
    protected static ?string $title = 'Jenjang';

    // Label untuk model (digunakan di beberapa action)
    protected static ?string $modelLabel = 'Jenjang';

    // Label plural untuk model
    protected static ?string $pluralModelLabel = 'Daftar Jenjang';

    protected static ?string $titleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nama Unit')
                ->required()
                ->maxLength(255),

            Forms\Components\Hidden::make('foundation_id') // Perhatikan penulisan 'foundation_id' bukan 'foundation_id'
                ->default(function () {
                    if (Auth::user()?->role !== 'superadmin') {
                        return Auth::user()->foundation_id;
                    }

                    return $this->getOwnerRecord()?->foundation_id;
                })
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Unit')
                    ->searchable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Unit')
                    ->mutateFormDataUsing(function (array $data): array {
                        // Debug data sebelum diubah
                        Log::info('Before MutateFormData', $data);

                        // Jika foundation_id belum ada
                        if (!isset($data['foundation_id'])) {
                            if (Auth::user()?->role !== 'superadmin') {
                                $data['foundation_id'] = Auth::user()->foundation_id;
                            } else {
                                $owner = $this->getOwnerRecord();
                                $data['foundation_id'] = $owner?->foundation_id;
                            }
                        }

                        // Debug data setelah diubah
                        Log::info('After MutateFormData', $data);
                        return $data;
                    })
                    ->using(function (array $data, string $model) {
                        // Debug sebelum create
                        Log::info('Before Create', $data);

                        $record = $this->getRelationship()->create($data);

                        // Debug setelah create
                        Log::info('After Create', $record->toArray());
                        return $record;
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function configureQuery(Builder $query): Builder
    {
        $owner = static::getOwnerRecord();

        if (!$owner) {
            return $query;
        }

        return $query->whereHas('academicYears', function ($q) use ($owner) {
            $q->where('academic_year_id', $owner->id)
                ->when(Auth::user()->role !== 'superadmin', function ($q2) {
                    $q2->wherePivot('foundation_id', Auth::user()->foundation_id);
                });
        });
    }
}
