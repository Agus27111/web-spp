<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\StudentResource\Pages;
use App\Filament\Admin\Resources\StudentResource\RelationManagers;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationLabel = 'Siswa';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Orang Tua')
                            ->schema([
                                Select::make('guardian_id')
                                    ->label('Pilih / Tambah Orang Tua')
                                    ->relationship('guardian', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label('Nama Orang Tua')
                                            ->required(),

                                        TextInput::make('phone_number')
                                            ->label('Nomor HP')
                                            ->tel()
                                            ->maxLength(255),
                                    ])
                                    ->required()
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Siswa')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Siswa')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('nisn')
                                    ->label('NISN')
                                    ->maxLength(255),

                                Forms\Components\FileUpload::make('image')
                                    ->label('Foto')
                                    ->image(),

                                Forms\Components\DatePicker::make('birth_date')
                                    ->label('Tanggal Lahir')
                                    ->required(),
                                Forms\Components\Repeater::make('studentAcademics')
                                    ->relationship('studentAcademics')
                                    ->label('Tahun Akademik')
                                    ->schema([
                                        Forms\Components\Select::make('academic_year_id')
                                            ->label('Tahun Ajaran')
                                            ->relationship('academicYear', 'name')
                                            ->required(),

                                        Forms\Components\Select::make('class_id')
                                            ->label('Kelas')
                                            ->relationship('classroom', 'name')
                                            ->required(),

                                        Forms\Components\Select::make('status')
                                            ->label('Status')
                                            ->options([
                                                'active' => 'Aktif',
                                                'inactive' => 'Tidak Aktif',
                                            ])
                                            ->required(),
                                    ])
                                    ->defaultItems(1)
                                    ->columns(3)
                                    ->columnSpanFull()

                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'latestStudentAcademic.classroom',
                'studentAcademics.academicYear',
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table

            ->columns([
                Tables\Columns\TextColumn::make('foundation.name')
                    ->label('Foundation')
                    ->visible(fn() => Auth::user()->role === 'superadmin')
                    ->searchable(),
                Tables\Columns\TextColumn::make('studentAcademics.academicYear.name')
                    ->label('Tahun Akademik'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nisn')
                    ->searchable(),
                Tables\Columns\TextColumn::make('latestStudentAcademic.classroom.name')
                    ->label('Kelas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('guardian.name')
                    ->label('Nama Orang Tua')
                    ->sortable(),
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
            // ->headerActions([
            //     Tables\Actions\CreateAction::make(),
            // ])
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
