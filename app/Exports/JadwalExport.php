<?php

namespace App\Exports;

use App\Models\JadwalKebaktian;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class JadwalExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $month;
    protected $year;
    protected $rowNumber = 1;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        return JadwalKebaktian::with(['tugas.user.bidang'])
            ->where('status', 'published')
            ->whereYear('tanggal_pelayanan', $this->year)
            ->whereMonth('tanggal_pelayanan', $this->month)
            ->orderBy('tanggal_pelayanan')
            ->orderBy('waktu_mulai')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Jenis Kebaktian',
            'Waktu',
            'Lokasi',
            'Tema',
            'Usher',
            'Pembicara',
            'Pendoa',
            'Praise & Worship',
            'Multimedia',
        ];
    }

    public function map($jadwal): array
    {
        $tugasPerBidang = $jadwal->tugas
            ->where('status_tugas', 'approved')
            ->filter(fn($t) => $t->user && $t->user->id_bidang)
            ->groupBy(fn($t) => $t->user->id_bidang);

        $formatPekerja = function($idBidang) use ($tugasPerBidang) {
            $pekerja = $tugasPerBidang[$idBidang] ?? collect();
            return $pekerja->map(function($t) {
                $nama = $t->user->nama ?? 'N/A';
                $peran = $t->peran_tugas ? " ({$t->peran_tugas})" : '';
                return $nama . $peran;
            })->join("\n");
        };

        return [
            $this->rowNumber++,
            $jadwal->tanggal_pelayanan->format('d/m/Y'),
            $jadwal->jenis_kebaktian,
            $jadwal->waktu_mulai . ' - ' . $jadwal->waktu_selesai,
            $jadwal->lokasi ?? '-',
            $jadwal->tema ?? '-',
            $formatPekerja(1) ?: '-', // Usher
            $formatPekerja(2) ?: '-', // Pembicara
            $formatPekerja(3) ?: '-', // Pendoa
            $formatPekerja(4) ?: '-', // PW
            $formatPekerja(5) ?: '-', // Multimedia
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '667EEA'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(25);

        $lastRow = $this->rowNumber;
        $sheet->getStyle("A2:K{$lastRow}")->applyFromArray([
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        for ($i = 2; $i <= $lastRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle("A{$i}:K{$i}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8F9FA'],
                    ],
                ]);
            }
            $sheet->getRowDimension($i)->setRowHeight(-1);
        }

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 12,  // Tanggal
            'C' => 18,  // Jenis Kebaktian
            'D' => 15,  // Waktu
            'E' => 15,  // Lokasi
            'F' => 30,  // Tema
            'G' => 25,  // Usher
            'H' => 25,  // Pembicara
            'I' => 25,  // Pendoa
            'J' => 25,  // PW
            'K' => 25,  // Multimedia
        ];
    }
}