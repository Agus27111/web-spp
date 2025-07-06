<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PaymentResource\Pages;
use App\Models\AcademicYear;
use App\Models\Discount;
use App\Models\Fee;
use App\Models\FeeType;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentAcademic;
use App\Models\StudentDiscount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Torgodly\Html2Media\Tables\Actions\Html2MediaAction;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationLabel = 'Pembayaran';
    protected static ?string $modelLabel = 'pembayaran';
    protected static ?string $pluralModelLabel = 'daftar pembayaran';
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['foundation', 'studentAcademicYear', 'fee']); // Eager load relasi

        return Auth::user()->hasRole('superadmin')
            ? $query
            : $query->where('foundation_id', Auth::user()->foundation_id);
    }

    protected static function calculateDiscount(Forms\Set $set, Get $get): void
    {
        $originalAmount = $get('original_amount') ?? 0;

        if (!$get('student_id') || $originalAmount <= 0) {
            $set('discount_applied', 0);
            $set('paid_amount', $originalAmount);
            $set('applied_discounts', []);
            return;
        }

        $discounts = StudentDiscount::with('discount')
            ->whereHas('discount', fn($q) => $q->where('is_active', true))
            ->where('student_id', $get('student_id'))
            ->where('is_active', true)
            ->get();

        $totalDiscount = 0;
        $appliedDiscounts = [];

        foreach ($discounts as $discount) {
            $amount = $discount->discount->type === 'percentage'
                ? ($originalAmount * ($discount->discount->amount / 100))
                : $discount->discount->amount;

            $totalDiscount += $amount;
            $appliedDiscounts[] = [
                'name' => $discount->discount->name,
                'amount' => $discount->discount->amount,
                'type' => $discount->discount->type,
                'calculated' => $amount,
            ];
        }

        $totalDiscount = min($totalDiscount, $originalAmount);
        $set('discount_applied', $totalDiscount);
        $set('paid_amount', max($originalAmount - $totalDiscount, 0));
        $set('applied_discounts', $appliedDiscounts);
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Untuk superadmin, tampilkan pilihan foundation
                Forms\Components\Select::make('foundation_id')
                    ->label('Yayasan')
                    ->relationship('foundation', 'name')
                    ->searchable()
                    ->required()
                    ->visible(fn() => Auth::user()->hasRole('superadmin'))
                    ->default(fn() => Auth::user()->foundation_id),


                Forms\Components\Hidden::make('foundation_id')
                    ->default(fn() => Auth::user()->foundation_id)
                    ->visible(fn() => !Auth::user()->hasRole('superadmin'))
                    ->dehydrated(),

                // Academic Year Selection
                Forms\Components\Hidden::make('student_academic_year_id')
                    ->dehydrated(),

                Forms\Components\Select::make('academic_year_id')
                    // ->label('Tahun Ajaran')
                    // ->relationship('academicYear', 'name')
                     ->options(
                            AcademicYear::where('foundation_id', auth()->user()->foundation_id)
                            ->pluck('name', 'id')
                        )
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn(Forms\Set $set) => $set('student_id', null)),

                // Student Selection
                Forms\Components\Select::make('student_id')
                    ->label('Siswa')
                    ->live()
                    ->searchable()
                    ->afterStateUpdated(function (Forms\Set $set, Get $get) {
                        self::calculateDiscount($set, $get);
                        $academicYearId = $get('academic_year_id');
                        $studentId = $get('student_id');

                        if ($academicYearId && $studentId) {
                            $studentAcademicYearId = \App\Models\StudentAcademic::where('academic_year_id', $academicYearId)
                                ->where('student_id', $studentId)
                                ->value('id');

                            $set('student_academic_year_id', $studentAcademicYearId);
                        }
                    
                    })
                    ->preload()
                    ->options(function (Get $get) {
                        if (!$get('academic_year_id')) return [];

                        return StudentAcademic::query()
                            ->where('academic_year_id', $get('academic_year_id'))
                            ->where('foundation_id', $get('foundation_id'))
                            ->with(['student' => function ($query) {
                                $query->select('id', 'name', 'nisn');
                            }])
                            ->get()
                            ->mapWithKeys(fn($item) => [
                                $item->student_id => $item->student->name . ' - NISN: ' . $item->student->nisn
                            ]);
                    })
                    ->getSearchResultsUsing(function (string $search, Get $get) {
                        if (!$get('academic_year_id')) return [];

                        return StudentAcademic::query()
                            ->where('academic_year_id', $get('academic_year_id'))
                            ->where('foundation_id', $get('foundation_id'))
                            ->whereHas('student', function ($query) use ($search) {
                                $query->where('name', 'like', "%{$search}%")
                                    ->orWhere('nisn', 'like', "%{$search}%");
                            })
                            ->with(['student' => function ($query) {
                                $query->select('id', 'name', 'nisn');
                            }])
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn($item) => [
                                $item->student_id => $item->student->name . ' - NISN: ' . $item->student->nisn
                            ]);
                    })
                    ->getOptionLabelUsing(function ($value) {
                        $student = Student::select('name', 'nisn')->find($value);
                        return $student ? $student->name . ' - NISN: ' . $student->nisn : '';
                    }),

                Forms\Components\Select::make('fee_type_id')
                    ->label('Jenis Pembayaran')
                    ->live()
                    ->options(function (Get $get) {
                        if (!$get('academic_year_id') || !$get('foundation_id')) return [];

                        return FeeType::query()
                            ->where('foundation_id', $get('foundation_id'))
                            ->where('academic_year_id', $get('academic_year_id'))
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->afterStateUpdated(function (Forms\Set $set, Get $get) {
                        $fee = Fee::where('fee_type_id', $get('fee_type_id'))
                            ->where('academic_year_id', $get('academic_year_id'))
                            ->first();

                        $originalAmount = $fee?->amount ?? 0;
                        $set('original_amount', $originalAmount);

                        // Jika langsung ingin menghitung paid_amount
                        $set('paid_amount', $originalAmount);

                        self::calculateDiscount($set, $get);

                         $academicYearId = $get('academic_year_id');
                        $feeTypeId = $get('fee_type_id');

                        if ($academicYearId && $feeTypeId) {
                            $feeId = \App\Models\Fee::where('academic_year_id', $academicYearId)
                                ->where('fee_type_id', $feeTypeId)
                                ->value('id');

                            $set('fee_id', $feeId);
                        }
                    }),


                Forms\Components\Hidden::make('fee_id')
                    ->dehydrated(),

                Forms\Components\Select::make('month')
                    ->label('Bulan')
                    ->options([
                        'Januari' => 'Januari',
                        'Februari' => 'Februari',
                        'Maret' => 'Maret',
                        'April' => 'April',
                        'Mei' => 'Mei',
                        'Juni' => 'Juni',
                        'Juli' => 'Juli',
                        'Agustus' => 'Agustus',
                        'September' => 'September',
                        'Oktober' => 'Oktober',
                        'November' => 'November',
                        'Desember' => 'Desember',
                    ])
                    ->required(),

                Forms\Components\DatePicker::make('payment_date')
                    ->label('Tanggal Pembayaran')
                    ->required()
                    ->default(now()),

                Forms\Components\TextInput::make('original_amount')
                    ->label('Jumlah Asli')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->reactive()
                    ->afterStateUpdated(function (Forms\Set $set, Get $get) {
                        self::calculateDiscount($set, $get);
                    }),

                Forms\Components\TextInput::make('discount_applied')
                    ->label('Total Diskon')
                    ->numeric()
                    ->default(0)
                    ->readOnly()
                    ->reactive(),

                Forms\Components\TextInput::make('paid_amount')
                    ->label('Jumlah Dibayar')
                    ->numeric()
                    ->readOnly()
                    ->reactive()
                    ->default(0)
                    ->dehydrated(),

                Forms\Components\Select::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->options([
                        'Tunai' => 'Tunai',
                        'Transfer' => 'Transfer',
                    ])
                    ->default('Tunai')
                    ->required(),

                Forms\Components\Section::make('Detail Diskon')
                    ->schema([
                        Forms\Components\Repeater::make('applied_discounts')
                            ->label('')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Diskon')
                                    ->disabled()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('type')
                                    ->label('Tipe')
                                    ->disabled(),

                                Forms\Components\TextInput::make('calculated')
                                    ->label('Nominal Diskon')
                                    ->disabled()
                                    ->numeric(),
                            ])
                            ->columns(4)
                            ->disabled(),

                        Forms\Components\TextInput::make('discount_applied') // sinkron dengan controller
                            ->label('Total Diskon')
                            ->numeric()
                            ->disabled(),
                    ])
                    ->visible(fn(Get $get) => filled($get('applied_discounts'))),


                Forms\Components\FileUpload::make('payment_proof')
                    ->label('Bukti Pembayaran')
                    ->directory('payment-proof')
                    ->image(),
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

                Tables\Columns\TextColumn::make('studentAcademicYear.student.name')
                    ->label('Siswa')
                    ->searchable(),

                Tables\Columns\TextColumn::make('studentAcademicYear.academicYear.name')
                    ->label('Tahun Ajaran'),

                Tables\Columns\TextColumn::make('fee.feeType.name')
                    ->label('Jenis Pembayaran')
                    ->state(function ($record) {
                        return $record->fee->feeType->name ?? '-';
                    }),

                Tables\Columns\TextColumn::make('month')
                    ->label('Bulan'),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Tanggal')
                    ->date(),

                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('Jumlah')
                    ->numeric()
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Metode'),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
               Tables\Actions\Action::make('print')
                    ->label('Cetak Struk')
                    ->icon('heroicon-o-printer')
                    ->url(fn($record) => route('payments.print', $record))
                    ->openUrlInNewTab(),

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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
            'print' => Pages\PrintPayment::route('/{record}/print'),
        ];
    }

    public static function beforeCreate(array $data): array
    {
        \Log::info('BeforeCreate Data:', $data);

        if (empty($data['student_academic_year_id'])) {
    $data['student_academic_year_id'] = StudentAcademic::where('student_id', $data['student_id'] ?? null)
        ->where('academic_year_id', $data['academic_year_id'] ?? null)
        ->value('id');
}

     
        if (empty($data['fee_id'])) {
            $data['fee_id'] = Fee::where('fee_type_id', $data['fee_type_id'])
                ->where('academic_year_id', $data['academic_year_id'])
                ->value('id');
        }

        // Pastikan student_academic_year_id terisi
        if (empty($data['student_academic_year_id'])) {
            $data['student_academic_year_id'] = StudentAcademic::where('student_id', $data['student_id'])
                ->where('academic_year_id', $data['academic_year_id'])
                ->value('id');
        }

        // Hanya kirim field yang diperlukan model
        return [
            'foundation_id' => $data['foundation_id'],
            'student_academic_year_id' => $data['student_academic_year_id'],
            'fee_id' => $data['fee_id'],
            'month' => $data['month'],
            'payment_date' => $data['payment_date'],
            'original_amount' => $data['original_amount'],
            'discount_applied' => $data['discount_applied'],
            'paid_amount' => $data['paid_amount'],
            'payment_method' => $data['payment_method'],
            'payment_proof' => $data['payment_proof'] ?? null,
            'applied_discounts' => $data['applied_discounts'] ?? null,
        ];
    }
}
