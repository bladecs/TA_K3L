<?php

namespace App\Exports;

use App\Models\PotentialHazardReport;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HazardGisSpreadsheetExport
{
    public function download(Collection $reports, array $filters): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('GIS Hazard');

        $headers = [
            'No',
            'No. Laporan',
            'Tanggal',
            'Nama Pelapor',
            'Judul Hazard',
            'Kategori Hazard',
            'Risk Level',
            'Status',
            'Lokasi',
            'Detail Lokasi',
            'Area',
            'Latitude',
            'Longitude',
            'Catatan',
        ];

        $lastColumn = Coordinate::stringFromColumnIndex(count($headers));

        $sheet->mergeCells("A1:{$lastColumn}1");
        $sheet->mergeCells("A2:{$lastColumn}2");
        $sheet->mergeCells("A3:{$lastColumn}3");
        $sheet->setCellValue('A1', 'Laporan GIS Hazard SIAGA POLMAN');
        $sheet->setCellValue('A2', 'Export data titik koordinat hazard potensial');
        $sheet->setCellValue('A3', 'Dibuat pada: ' . now()->format('d M Y H:i'));

        $sheet->fromArray($headers, null, 'A6');

        $row = 7;
        foreach ($reports->values() as $index => $report) {
            $sheet->fromArray($this->row($report, $index + 1), null, "A{$row}");
            $row++;
        }

        $summaryRow = $row + 1;
        $sheet->mergeCells("A{$summaryRow}:{$lastColumn}{$summaryRow}");
        $sheet->setCellValue("A{$summaryRow}", 'Total data: ' . $reports->count() . ' | Filter: ' . $this->filterSummary($filters));

        $sheet->freezePane('A7');
        $sheet->setAutoFilter("A6:{$lastColumn}" . max(6, $row - 1));

        $this->styleSheet($spreadsheet, $row, $lastColumn);

        $writer = new Xlsx($spreadsheet);
        $fileName = 'laporan-gis-hazard-' . now()->format('Ymd-His') . '.xlsx';

        return response()->streamDownload(function () use ($writer, $spreadsheet) {
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
        ]);
    }

    protected function row(PotentialHazardReport $report, int $number): array
    {
        $locationName = $report->location?->name ?? '-';

        return [
            $number,
            $report->report_number,
            optional($report->submitted_at)->format('d M Y'),
            $report->reporter?->name ?? $report->reporter_name ?? '-',
            $report->title,
            $report->hazard_type ? str_replace('-', ' ', $report->hazard_type) : '-',
            $report->risk_level ?: '-',
            $report->status,
            $locationName,
            $report->specific_location ?? '-',
            $locationName === 'Diluar Polman' ? 'Diluar Polman' : 'Di Dalam Polman',
            $report->latitude,
            $report->longitude,
            $report->notes ?: '-',
        ];
    }

    protected function filterSummary(array $filters): string
    {
        $labels = [
            'q' => 'Nama/Laporan',
            'hazard_type' => 'Kategori',
            'risk_level' => 'Risk Level',
            'status' => 'Status',
            'location_id' => 'Lokasi',
            'scope' => 'Area',
            'date_from' => 'Dari',
            'date_to' => 'Sampai',
            'month' => 'Bulan',
            'year' => 'Tahun',
        ];

        $active = collect($filters)
            ->filter(fn ($value) => filled($value))
            ->map(fn ($value, $key) => ($labels[$key] ?? $key) . '=' . $value)
            ->values()
            ->implode('; ');

        return $active !== '' ? $active : 'Semua data';
    }

    protected function styleSheet(Spreadsheet $spreadsheet, int $rowAfterData, string $lastColumn): void
    {
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18)->getColor()->setRGB('0F172A');
        $sheet->getStyle('A2:A3')->getFont()->getColor()->setRGB('475569');

        $sheet->getStyle("A6:{$lastColumn}6")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0A4DB3'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $dataEndRow = max(6, $rowAfterData - 1);
        $sheet->getStyle("A6:{$lastColumn}{$dataEndRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true,
            ],
        ]);

        foreach (range(1, Coordinate::columnIndexFromString($lastColumn)) as $column) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($column))->setAutoSize(true);
        }

        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->getRowDimension(6)->setRowHeight(24);
    }
}
