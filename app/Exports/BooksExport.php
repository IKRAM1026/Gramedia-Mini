<?php

namespace App\Exports;

use App\Models\Book;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BooksExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    private int $number = 0; // inisiasi dimulai dari 0

    public function collection(): Collection // untuk mengambil data dari database
    {
        return Book::with('bookCategory')->get();
    }

    public function headings(): array 
    {
        return [
            'Nomor',
            'Alamat Gambar Sampul Buku',
            'Nama Kategori Buku',
            'Judul Buku',
            'Penulis',
            'Penerbit',
            'Tanggal Rilis',
            'Jumlah Halaman',
            'Bahasa',
            'Harga',
        ];
    }

    public function map($book): array
    {
        return [
            ++$this->number,
            asset($book->cover),
            $book->bookCategory ? $book->bookCategory->name : '',
            $book->title,
            $book->writer,
            $book->publisher,
            date('d F Y', strtotime($book->release_date)),
            $book->page_of_book,
            $book->language,
            'Rp '.number_format((float) $book->price, 0, ',', '.'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $totalRows = $this->collection()->count() + 1;

        return [
            1 => ['font' => ['bold' => true]],
            'A1:J'.$totalRows => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];
    }
}
