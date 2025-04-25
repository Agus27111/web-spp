<?php

namespace App\Imports;

use App\Models\Guardian;
use App\Models\Student;
use App\Models\StudentAcademic;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $data = $row->toArray();

        // 1. Buat user orang tua
        $user = User::firstOrCreate(
            ['email' => $data['email_ortu']],
            [
                'name' => $data['nama_orangtua'],
                'password' => Hash::make('12345678'),
                'role' => 'parent',
                'phone_number' => $data['nomor_hp'],
            ]
        );

        // 2. Buat atau ambil guardian
        $guardian = Guardian::firstOrCreate([
            'name' => $data['nama_orangtua'],
            'phone_number' => $data['nomor_hp'],
        ]);

        // 3. Cek jika siswa sudah ada berdasarkan NISN
        $student = Student::firstOrCreate(
            ['nisn' => $data['nisn']],
            [
                'guardian_id' => $guardian->id,
                'name' => $data['nama_siswa'],
                'birth_date' => $data['tgl_lahir'],
            ]
        );

        // 4. Simpan ke student_academics
        // Perlu pastikan ada ID tahun_ajaran dan ID kelas di CSV
        if (!empty($data['id_tahun_ajaran']) && !empty($data['id_kelas'])) {
            StudentAcademic::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'academic_year_id' => $data['id_tahun_ajaran'],
                ],
                [
                    'class_id' => $data['id_kelas'],
                    'status' => $data['status'], 
                ]
            );
        }
    }
}
