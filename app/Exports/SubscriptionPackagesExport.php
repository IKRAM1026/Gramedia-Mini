<?php

namespace App\Exports;

use App\Models\SubscriptionPackage;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SubscriptionPackagesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    private int $number = 0;

    public function collection(): Collection
    {
        return SubscriptionPackage::query()
            ->withCount('books')
            ->withCount([
                'subscriptionsPackageUsers as active_users_count' => function ($query) {
                    $query->whereDate('expired_date', '>=', today());
                },
            ])
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nomor',
            'Nama Paket Langganan',
            'Harga',
            'Jumlah Buku',
            'Pelanggan Aktif',
        ];
    }

    public function map($package): array
    {
        return [
            ++$this->number,
            $package->nama_package,
            'Rp '.number_format((float) $package->price, 0, ',', '.'),
            $package->books_count,
            $package->active_users_count,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $totalRows = $this->collection()->count() + 1;

        return [
            1 => ['font' => ['bold' => true]],
            'A1:E'.$totalRows => [
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
