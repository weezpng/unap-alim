<?php

namespace App\Exports;

use App\Models\hospede;
use App\Models\marcacaotable;
use App\Models\entradasQuiosque;
use App\Models\unap_unidades;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;


class MonthlyExportMesses implements FromArray, ShouldAutoSize, WithHeadings,WithColumnWidths,WithStyles,WithTitle,WithEvents
{
  public function __construct($month)
  {
    set_time_limit(240);
    $date = 'Y-'.$month.'-d';

    $this->date = date($date);
    $this->start_date = date('Y-m-01', strtotime($this->date));
    $this->end_date = date('Y-m-t', strtotime($this->date));

    $this->LAST_LINE = (hospede::count()) + 1 + (unap_unidades::count());
    $this->HEADER_LINE = 'A1:J1';
    $this->SECOND_HEADER_LINE = 'E2:J2';
    $this->range = '';
  }

  public function registerEvents(): array
  {
      return [
          AfterSheet::class => function(AfterSheet $event) {
            $event->sheet->getDelegate()->freezePane('A2');
            $event->sheet->getDelegate()->freezePane('A3');
            $event->sheet->getStyle($this->range)->applyFromArray([
                'borders' => [
                    'horizontal' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
            ])->getAlignment()->setWrapText(true);

            $highestRow = $event->sheet->getHighestRow(); // e.g. 10
            $highestCol = $event->sheet->getHighestColumn();
            for ($i=4; $i <= $highestRow; $i+=2) {
              $cell = 'A'.$i;
              $range = 'A'.$i.':'.$highestCol.$i;
              $event->sheet->getStyle($range)->applyFromArray([
                  'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' =>'E0E0E0']
                ]
              ]);
            }
          },
      ];
  }

  public function title(): string
  {
    $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
    $semana  = array("Segunda-Feira", "Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
    $mes_index = date('m', strtotime($this->start_date));
    $weekday_number = date('N',  strtotime($this->start_date));
    $title = date('d', strtotime($this->start_date)).' '.$mes[($mes_index - 1)].' '.date('Y', strtotime($this->start_date)). " a ";

    $mes_index_2 = date('m', strtotime($this->end_date));
    $weekday_number_2 = date('N',  strtotime($this->end_date));
    $title = "Hóspedes (".$mes[($mes_index - 1)].")";

    return $title;
  }

 public function headings(): array
 {
   return [
       ['QUARTO', 'NOME', 'TIPO', 'LOCAL', 'PEQUENO-ALMOÇO', '', 'ALMOÇO', '', 'JANTAR', '', ''],
       ['', '', '', '', 'MARCADAS', 'CONSUMIDAS','MARCADAS', 'CONSUMIDAS','MARCADAS', 'CONSUMIDAS'],
    ];
 }

 public function columnWidths(): array
 {
     return [
         'A' => 14,  // NIM
         'B' => 60,  // NOME
         'C' => 23,  // POSTO
         'D' => 44,  // UNIDADE
         'E' => 17,  // 1ºREF - ,
         'F' => 17,  // 1ºREF - > MERGE
         'G' => 17,  // 2ºREF - ,
         'H' => 17,  // 2ºREF - > MERGE
         'I' => 17,  // 3ºREF- ,
         'J' => 17,  // 3ºREF - > MERGE
     ];
 }

 public function styles(Worksheet $sheet)
 {

   $TAGS_CONS_FILTER = (string)"C1:J".$this->LAST_LINE;

   $sheet->getStyle($this->HEADER_LINE)
         ->getFont()
         ->setBold(true)
         ->setSize(12);

   $sheet->getStyle($this->HEADER_LINE)
         ->getFill()
         ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
         ->getStartColor()
         ->setARGB('755874');

 $sheet->getStyle($this->HEADER_LINE)
       ->getFont()
       ->getColor()
       ->setARGB('ffffff');

   $sheet->getStyle($this->SECOND_HEADER_LINE)
         ->getFill()
         ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
         ->getStartColor()
         ->setARGB('755874');

   $sheet->getStyle($this->SECOND_HEADER_LINE)
         ->getFont()
         ->getColor()
         ->setARGB('ffffff');

   $sheet->getStyle('A:J')
         ->getAlignment()
         ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
         ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

   $sheet->mergeCells('E1:F1')
         ->mergeCells('G1:H1')
         ->mergeCells('I1:J1');

   $sheet->setAutoFilter(
     $sheet->calculateWorksheetDimension()
   );

   $this->range = $sheet->calculateWorksheetDimension();
   str_replace('A1', 'A3', $this->range);

   $sheet->getRowDimension('1')->setRowHeight(20);
   $sheet->getRowDimension('2')->setRowHeight(18);
 }


   public function array() : array
   {
     return $this->GetData();
   }

   public function GetData(){
     $info = array();
     $users = hospede::orderBy('quarto')
                  ->orderBy('local')
                  ->orderBy('type_temp')
                  ->orderBy('type')
                  ->get()->all();

     $it = 0;
     $last_unit = '';
     $first_run = true;

     foreach ($users as $key => $user) {

       if ($last_unit != $user['local']) {
         if ($first_run) {
           $first_run = false;
           $info[$it][0] = '';
           $info[$it][1] = '';
           $info[$it][2] = '';
           $info[$it][3] = '';
           $info[$it][4] = '';
           $info[$it][5] = '';
           $info[$it][6] = '';
           $info[$it][7] = '';
           $info[$it][8] = '';
           $info[$it][9] = '';
           $it++;
         } else {
           $info[$it][0] = '-';
           $info[$it][1] = '-';
           $info[$it][2] = '-';
           $info[$it][3] = '-';
           $info[$it][4] = '-';
           $info[$it][5] = '-';
           $info[$it][6] = '-';
           $info[$it][7] = '-';
           $info[$it][8] = '-';
           $info[$it][9] = '-';
           $it++;
         }
       }

       $NIM = $user['fictio_nim'];

       $unidade = unap_unidades::where('slug', $user['local'])->get('name')->first();

       $info[$it][0] = (($user['quarto']!="") ? $user['quarto'] : 'N/D');
       $info[$it][1] = strtoupper($user['name']);
       $info[$it][2] = (($user['type']!="") ? $user['type'] : 'N/D') . ' (' . (($user['type_temp'] == "PERM") ? "Permanente" : "Temporário").')';
       $info[$it][3] = $unidade['name'];

       $tag = marcacaotable::where('meal', '1REF')->where('data_marcacao', '>=', $this->start_date)->where('data_marcacao', '<=', $this->end_date)->where('NIM', $NIM)->count();
       $ent = entradasQuiosque::where('REF', '1REF')->where('REGISTADO_DATE', '>=', $this->start_date)->where('REGISTADO_DATE', '<=', $this->end_date)->where('NIM', $NIM)->count();
       $info[$it][4] = ($tag != 0) ? $tag : '0';
       $info[$it][5] = ($ent != 0) ? $ent : '0';


       $tag = marcacaotable::where('meal', '2REF')->where('data_marcacao', '>=', $this->start_date)->where('data_marcacao', '<=', $this->end_date)->where('NIM', $NIM)->count();
       $ent = entradasQuiosque::where('REF', '2REF')->where('REGISTADO_DATE', '>=', $this->start_date)->where('REGISTADO_DATE', '<=', $this->end_date)->where('NIM', $NIM)->count();
       $info[$it][6] = ($tag != 0) ? $tag : '0';
       $info[$it][7] = ($ent != 0) ? $ent : '0';

       $tag = marcacaotable::where('meal', '3REF')->where('data_marcacao', '>=', $this->start_date)->where('data_marcacao', '<=', $this->end_date)->where('NIM', $NIM)->count();
       $ent = entradasQuiosque::where('REF', '3REF')->where('REGISTADO_DATE', '>=', $this->start_date)->where('REGISTADO_DATE', '<=', $this->end_date)->where('NIM', $NIM)->count();
       $info[$it][8] = ($tag != 0) ? $tag : '0';
       $info[$it][9] = ($ent != 0) ? $ent : '0';
       $last_unit = $user['local'];
       $it++;
     }
     return $info;
   }

}
