<?php

namespace App\Imports;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Foundation;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\StudentAcademic;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements OnEachRow, WithHeadingRow
{
    protected $foundationId;

    public function __construct($foundationId)
    {
        $this->foundationId = $foundationId;
    }

    public function onRow(Row $row)
    {


        $data = $row->toArray();

        $birthDate = $data['tgl_lahir'];

        try {
            // Coba parsing otomatis
            $birthDate = Carbon::parse($birthDate)->format('Y-m-d');
        } catch (\Exception $e) {
            $birthDate = null;
        }

        if (!Auth::check()) {
            return;
        }

        // Menggunakan foundationId yang diterima dari constructor
        $foundationId = $this->foundationId;

        // Pastikan foundationId valid
        if (!$foundationId) {
            return;  // Jika foundation_id tidak ditemukan, tidak lanjutkan
        }

        // Lanjutkan proses import seperti biasa...
        $academicYear = AcademicYear::firstOrCreate(
            [
                'name' => $data['tahun_ajaran'],
                'foundation_id' => $foundationId
            ],
            [
                'is_active' => true
            ]
        );

        // Buat atau ambil unit
        $unit = Unit::firstOrCreate(
            [
                'name' => $data['unit'],
                'foundation_id' => $foundationId
            ]
        );

        // Buat atau ambil kelas
        $class = Classroom::firstOrCreate(
            [
                'name' => $data['nama_kelas'],
                'foundation_id' => $foundationId,
                'unit_id' => $unit->id
            ]
        );

        // 1. Buat user orang tua
        $user = User::firstOrCreate(
            ['email' => $data['email_ortu']],
            [
                'name' => $data['nama_orangtua'],
                'password' => Hash::make('12345678'),
                'role' => 'parent',
                'phone_number' => $data['nomor_hp'],
                'foundation_id' => $foundationId,
            ]
        );

        // 2. Buat atau ambil guardian
        $guardian = Guardian::firstOrCreate([
            'name' => $data['nama_orangtua'],
            'phone_number' => $data['nomor_hp'],
            'foundation_id' => $foundationId,
        ]);

        // 3. Cek jika siswa sudah ada berdasarkan NISN
        $student = Student::firstOrCreate(
            ['nisn' => $data['nisn']],
            [
                'guardian_id' => $guardian->id,
                'name' => $data['nama_siswa'],
                'birth_date' => $birthDate,
                'foundation_id' => $foundationId,
            ]
        );

        // Simpan ke student_academics
        StudentAcademic::updateOrCreate(
            [
                'student_id' => $student->id,
                'academic_year_id' => $academicYear->id,
            ],
            [
                'class_id' => $class->id,
                'status' => $data['status'],
                'foundation_id' => $foundationId,
            ]
        );
    }
}
