<?php

namespace App\Exports;

use App\Models\ShippingInstructionHeader;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CementReceiveReportExport implements FromView, WithStyles, ShouldAutoSize
{
    use Exportable;

    protected $id;

    public function __construct(int $id) {

        $this->id = $id;
    }

    public function view(): View
    {
        $si = ShippingInstructionHeader::with([
            'shipping_instruction_monitors'])
            ->find($this->id);

        $manifestNumber = DB::table('shipping_instruction_monitors')
            ->where('header_id', '=', $this->id)
            ->count();

        return view('exports.cement_receive_report', [
            'siHeader' => $si,
            'manifestNumber' => $manifestNumber. ' x 20"'
        ]);
    }

    public function styles(Worksheet $sheet){
        return [
            'A1' => ['font' => ['size' => 16, 'bold' => true]],
            'A2' => ['font' => ['size' => 11]],
            'A3' => ['font' => ['size' => 11]],
            'E2' => ['font' => ['size' => 11]],
            'E3' => ['font' => ['size' => 11]],
            'A5' => ['font' => ['color' => ['argb' => 'FF0000']]],
            'C5' => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'F5F505']]
            ],
            'M5' => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'F5F505']]
            ],
            'A6:R6' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000']
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]
            ]
        ];
    }
}
