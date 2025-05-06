<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;

class StudentsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Student::with([
                'guardian',
                'latestStudentAcademic.classroom',
                'studentAcademics.academicYear',
                'foundation'
            ])
            ->get()
            ->map(function ($student) {
                return [
                    'Nama Siswa' => $student->name,
                    'NISN' => $student->nisn,
                    'Tanggal Lahir' => $student->birth_date,
                    'Nama Orang Tua' => optional($student->guardian)->name,
                    'Nomor HP Orang Tua' => optional($student->guardian)->phone_number,
                    'Yayasan' => optional($student->foundation)->name,
                    'Tahun Ajaran' => optional($student->latestStudentAcademic?->academicYear)->name,
                    'Kelas' => optional($student->latestStudentAcademic?->classroom)->name,
                    'Status' => optional($student->latestStudentAcademic)->status,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Nama Siswa',
            'NISN',
            'Tanggal Lahir',
            'Nama Orang Tua',
            'Nomor HP Orang Tua',
            'Yayasan',
            'Tahun Ajaran',
            'Kelas',
            'Status',
        ];
    }
}
