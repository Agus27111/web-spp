<?php

namespace App\Filament\Admin\Resources\DiscountResource\Pages;

use App\Filament\Admin\Resources\DiscountResource;
use App\Models\AcademicYear;
use App\Models\Discount;
use App\Models\FeeType;
use App\Models\StudentAcademic;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class ManageDiscountStudents extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = DiscountResource::class;
    protected static string $view = 'admin.discounts.manage-students';

    public ?array $data = [];
    public Discount $discount;
    public bool $saveAllStudents = false;

    public function mount(Discount $record): void
    {
        $this->discount = $record;
        $this->form->fill([
            'academic_year_id' => AcademicYear::where('foundation_id', $record->foundation_id)
                ->where('is_active', true)
                ->first()?->id,
            'student_ids' => $record->studentDiscounts->pluck('student_id')->toArray()
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->options(function () {
                        return AcademicYear::where('foundation_id', $this->discount->foundation_id)
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->live()
                    ->afterStateUpdated(function () {
                        $this->updateStudentOptions();
                    }),

                Select::make('student_ids')
                    ->label('Siswa')
                    ->multiple()
                    ->options(function (Get $get) {
                        if (!$get('academic_year_id')) return [];
                        return StudentAcademic::with('student')
                            ->where('academic_year_id', $get('academic_year_id'))
                            ->get()
                            ->pluck('student.name', 'student.id');
                    })
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search, Get $get) {
                        if (!$get('academic_year_id')) return [];
                        return StudentAcademic::with('student')
                            ->whereHas('student', fn($q) => $q->where('name', 'like', "%{$search}%"))
                            ->where('academic_year_id', $get('academic_year_id'))
                            ->limit(50)
                            ->get()
                            ->pluck('student.name', 'student.id');
                    })
                    ->optionsLimit(100)
                    ->required()
                    ->visible(fn(Get $get): bool => !$get('save_all_students'))
                    ->suffixAction(
                        Action::make('selectAll')
                            ->label('Pilih Semua')
                            ->action(function (Set $set, Get $get) {
                                $options = StudentAcademic::with('student')
                                    ->where('academic_year_id', $get('academic_year_id'))
                                    ->get()
                                    ->pluck('student.name', 'student.id');
                                $set('student_ids', array_keys($options->toArray()));
                            })
                    )
                    ->columnSpanFull(),

                Select::make('fee_type_id')
                    ->label('Jenis Pembayaran')
                    ->options(FeeType::where('foundation_id', $this->discount->foundation_id)
                        ->pluck('name', 'id'))
                    ->required(),

                Toggle::make('save_all_students')
                    ->label('Berlaku untuk semua siswa di tahun ajaran ini')
                    ->live()
                    ->afterStateUpdated(function (Set $set, $state) {
                        if ($state) {
                            $this->js(<<<JS
                                if (!confirm('Apakah Anda yakin ingin menerapkan diskon untuk SEMUA siswa di tahun ajaran ini?')) {
                                    \$wire.set('data.save_all_students', false);
                                } else {
                                    \$wire.set('data.student_ids', []);
                                }
                            JS);
                        } else {
                            $set('student_ids', []);
                        }
                    })
                    ->columnSpanFull(),


                Placeholder::make('total_students')
                    ->content(function (Get $get) {
                        if (!$get('academic_year_id')) return '0 siswa';
                        $count = StudentAcademic::where('academic_year_id', $get('academic_year_id'))->count();
                        return "Total {$count} siswa di tahun ajaran ini";
                    })
                    ->columnSpanFull()
            ])
            ->statePath('data');
    }


    // Tambahkan method baru
    public function confirmSaveAll(): void
    {
        $this->form->fill(['save_all_students' => true]);
    }

    public function cancelSaveAll(): void
    {
        $this->form->fill(['save_all_students' => false]);
    }

    protected function updateTotalStudentsCount(): void
    {
        $this->form->getComponent('total_students')
            ->content(function (Get $get) {
                $academicYearId = $get('academic_year_id');
                if (!$academicYearId) return '0 siswa';

                $count = StudentAcademic::where('academic_year_id', $academicYearId)->count();
                return "Total {$count} siswa di tahun ajaran ini";
            });
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Hapus diskon sebelumnya
        $this->discount->studentDiscounts()->delete();

        // Tentukan siswa yang akan dapat diskon
        $studentIds = $data['save_all_students']
            ? $this->getAllStudentIds($data['academic_year_id'])
            : $data['student_ids'];

        // Simpan diskon baru
        foreach ($studentIds as $studentId) {
            $this->discount->studentDiscounts()->create([
                'student_id' => $studentId,
                'fee_type_id' => $data['fee_type_id'],
                'foundation_id' => $this->discount->foundation_id
            ]);
        }

        Notification::make()
            ->title('Berhasil!')
            ->body($data['save_all_students']
                ? 'Diskon diterapkan untuk semua siswa'
                : 'Diskon diterapkan untuk siswa terpilih')
            ->success()
            ->send();
    }

    protected function getAllStudentIds($academicYearId): array
    {
        return StudentAcademic::where('academic_year_id', $academicYearId)
            ->pluck('student_id')
            ->toArray();
    }

    protected function updateStudentOptions(): void
    {
        $this->form->getComponent('student_ids')
            ->options(function () {
                $academicYearId = $this->form->getState()['academic_year_id'];
                return StudentAcademic::with('student')
                    ->where('academic_year_id', $academicYearId)
                    ->get()
                    ->pluck('student.name', 'student.id');
            });
    }
}
