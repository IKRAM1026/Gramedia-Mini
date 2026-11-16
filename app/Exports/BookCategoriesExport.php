<?php

namespace App\Exports;

use App\Models\BookCategory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;


class BookCategoriesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
//fromCollection : untuk mengambil data dari database
//withHeadings : untuk menampilkan th dari excelnya
//withMapping : untuk menampilkan data yang akan di ambil ke dalam file excel
//withStyles : untuk menambahkan gaya pada file excel
{
    public function collection(): Collection
    {   //collection : mengambil data yang akan di ambil ke dalam file excel
        return BookCategory::with('book')->get();
    }
    //headings : untuk menampilkan th dari excelnya
    public function headings(): array
    {
        //menentukan th dari excelnya
        return [
            'No',
            'Nama Kategori',
            'Jumlah Buku',
        ];
    }

    private $no = 0;
    //map : untuk menampilkan data yang akan di ambil ke dalam file excel
    public function map($item): array {
        return [
            ++$this->no, // pre-increment untuk menambahkan angka sebelum di muncuin di excel
            $item->name, 
            $item->book ? $item->book->count() : 0, //jika ada relasi ke books hitung jumlah buku jika tidak ada relasi munculkan angka 0
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        //hitung baris untuk dikasi border
        $totalRows = $this->collection()->count() + 1; // +1 untuk menambahkan baris header
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
            // Dari kolom A ke C itu di kasih border
            'A1:C' . ($totalRows) => [
    
                'borders' => [
                    //kalau hanya bordernya di kanan maka 'right' , dst 
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'], // untuk menambahkan warna hitam pada border
                    ],
                ],
            ],
        ];
    }
}
