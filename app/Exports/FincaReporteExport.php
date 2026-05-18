<?php

namespace App\Exports;

use App\Models\Finca;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FincaReporteExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected Finca $finca;

    public function __construct(Finca $finca)
    {
        $this->finca = $finca;
    }

    public function collection()
    {
        return $this->finca->animales()
            ->with(['estado', 'ultimoHistorial'])
            ->orderBy('numero_arete')
            ->get();
    }

    public function headings(): array
    {
        return [
            'N° Arete',
            'Nombre',
            'Sexo',
            'Raza',
            'Estado',
            'Fecha de nacimiento',
            'Último peso (kg)',
            'Fecha último pesaje',
        ];
    }

    public function map($animal): array
    {
        return [
            $animal->numero_arete,
            $animal->nombre ?? 'Sin nombre',
            ucfirst($animal->sexo),
            $animal->raza,
            $animal->estado?->nombre_estado ?? '—',
            $animal->fecha_nacimiento?->format('d/m/Y') ?? '—',
            $animal->ultimoHistorial
                ? ($animal->ultimoHistorial->peso_real ?? $animal->ultimoHistorial->peso)
                : '—',
            $animal->ultimoHistorial?->created_at?->format('d/m/Y') ?? '—',
        ];
    }

    public function title(): string
    {
        return 'Reporte ' . substr($this->finca->nombre, 0, 25);
    }

    public function styles(Worksheet $sheet)
    {
        // Header con color verde oscuro
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F2E2E'],
            ],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Auto-size columnas
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}