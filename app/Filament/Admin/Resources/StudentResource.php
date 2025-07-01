<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\StudentResource\Pages;
use App\Models\Classroom;
use App\Models\Foundation;
use App\Models\Student;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\Action;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsExport;
use Symfony\Component\HttpFoundation\Response;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationLabel = 'Siswa';

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Orang Tua')
                            ->schema([
                                Select::make('foundation_id')
                                    ->label('Yayasan / Foundation')
                                    ->options(function () {
                                        $user = Auth::user();
                                        if ($user->role === 'superadmin') {
                                            return Foundation::pluck('name', 'id');
                                        }

                                        // Operator hanya melihat yayasan mereka
                                        return Foundation::where('id', $user->foundation_id)->pluck('name', 'id');
                                    })
                                    ->default(function () {
                                        $user = Auth::user();
                                        return $user->foundation_id;
                                    })
                                    ->disabled(fn() => Auth::user()->role !== 'superadmin')
                                    ->required()
                                    ->columnSpanFull(),

                                Select::make('guardian_id')
                                    ->label('Pilih / Tambah Orang Tua')
                                    ->relationship('guardian', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Select::make('foundation_id')
                                            ->label('Yayasan')
                                            ->options(Foundation::pluck('name', 'id'))
                                            ->required()
                                            ->visible(fn() => Auth::user()->role === 'superadmin'),

                                        Hidden::make('foundation_id')
                                            ->default(fn() => Auth::user()->foundation_id)
                                            ->visible(fn() => Auth::user()->role !== 'superadmin'),

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
                                            ->required()
                                            ->reactive(),

                                        Select::make('unit_id')
                                            ->label('Unit')
                                            ->live()
                                            ->options(function (callable $get) {
                                                $user = Auth::user();
                                                $academicYearId = $get('academic_year_id');

                                                if (!$academicYearId) {
                                                    return [];
                                                }

                                                // Debugging
                                                logger()->info('Academic Year ID: ' . $academicYearId);

                                                $units = Unit::whereHas('academicYears', function ($query) use ($academicYearId) {
                                                    $query->where('academic_years.id', $academicYearId);
                                                })
                                                    ->when($user->role !== 'superadmin', function ($query) use ($user) {
                                                        $query->where('units.foundation_id', $user->foundation_id);
                                                    })
                                                    ->get();

                                                logger()->info('Units found: ' . $units->count());

                                                return $units->pluck('name', 'id');
                                            })
                                            ->required(),


                                        Forms\Components\Select::make('class_id')
                                            ->label('Kelas')
                                            ->options(function (callable $get) {
                                                $unitId = $get('unit_id');
                                                if (!$unitId) {
                                                    return [];
                                                }

                                                return Classroom::where('unit_id', $unitId)
                                                    ->pluck('name', 'id');
                                            })
                                            ->live()
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
            ->headerActions([
                Action::make('Export Excel')
                    ->label('Export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (): Response {
                        return Excel::download(new StudentsExport, 'students.xlsx');
                    }),
                Action::make('Download Template')
                    ->label('Download Template')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(asset('storage/templates/template_import_siswa.xlsx'))
                    ->openUrlInNewTab(),
            ])
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
