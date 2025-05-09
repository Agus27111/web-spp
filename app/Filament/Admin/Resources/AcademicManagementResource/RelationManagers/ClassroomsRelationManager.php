<?php

namespace App\Filament\Admin\Resources\AcademicManagementResource\RelationManagers;

use App\Models\AcademicYear;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ClassroomsRelationManager extends RelationManager
{
    protected static string $relationship = 'classrooms';
    protected static ?string $titleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Kelas')
                    ->required(),

                Forms\Components\Select::make('unit_id')
                    ->label('Unit')
                    ->relationship(
                        name: 'unit',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query) {
                            $academicYear = $this->getOwnerRecord();
                            return $query->where('foundation_id', $academicYear->foundation_id)
                                ->whereHas('academicYears', function ($q) use ($academicYear) {
                                    $q->where('academic_year_id', $academicYear->id);
                                });
                        }
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Hidden::make('academic_year_id')
                    ->default($this->getOwnerRecord()->id),

                Forms\Components\Hidden::make('foundation_id')
                    ->default($this->getOwnerRecord()->foundation_id)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $academicYear = $this->getOwnerRecord();

                $query->whereHas('unit', function ($q) use ($academicYear) {
                    $q->where('foundation_id', $academicYear->foundation_id)
                        ->whereHas('academicYears', function ($q2) use ($academicYear) {
                            $q2->where('academic_year_id', $academicYear->id);
                        });
                });
            })
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kelas')
                    ->searchable(),

                Tables\Columns\TextColumn::make('unit.name')
                    ->label('Unit')
                    ->searchable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Kelas')
                    ->mutateFormDataUsing(function (array $data): array {
                        $academicYear = $this->getOwnerRecord();
                        $data['academic_year_id'] = $academicYear->id;
                        $data['foundation_id'] = $academicYear->foundation_id;
                        return $data;
                    }),
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
}
