<?php

namespace App\Helpers;

class ColumnLabelHelper
{
    public static function translate(string $column): string
    {
        $map = [
            'name'          => 'Nama',
            'email'         => 'Email',
            'phone'         => 'Nomor Telepon',
            'phone_number'  => 'Nomor Telepon',
            'nidn'          => 'NIDN',
            'nip'           => 'NIP',
            'position'      => 'Jabatan',
            'address'       => 'Alamat',
            'study_program' => 'Program Studi',
            'faculty'       => 'Fakultas',
            'department'    => 'Program Studi',
            'student_academic_services' => 'Layanan Akademik Mahasiswa',
            'bio'           => 'Biodata',
            'photo'         => 'Foto',
            'slug'          => 'Slug',
        ];

        return $map[$column]
            ?? ucwords(str_replace('_', ' ', $column));
    }
}
