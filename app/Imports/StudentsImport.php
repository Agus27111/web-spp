<?php

namespace App\Imports;

use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $user = User::firstOrCreate(
            ['email' => $row['email_ortu']],
            [
                'name' => $row['nama_orangtua'],
                'password' => Hash::make('12345678'),
                'role' => 'parent',  // Role orangtua
                'phone_number' => $row['nomor_hp'],  // Nomor HP
            ]
        );

        $guardian = Guardian::firstOrCreate([
            'name' => $row['nama_orangtua'],
            'phone_number' => $row['nomor_hp'],
        ]);

        // Cek duplikasi siswa berdasarkan NISN
        $existingStudent = Student::where('nisn', $row['nisn'])->first();
        if ($existingStudent) {
            return null;  // Return null jika sudah ada siswa dengan NISN yang sama
        }

        // Menambahkan data siswa baru
        return new Student([
            'guardian_id' => $guardian->id,
            'name' => $row['nama_siswa'],
            'nisn' => $row['nisn'],
            'birth_date' => $row['tgl_lahir'],
        ]);
    }
}
