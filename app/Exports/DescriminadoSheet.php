<?php

namespace App\Exports;

use App\Models\locaisref;
use App\Models\unap_unidades;
use App\Models\marcacaotable;
use App\Models\entradasQuiosque;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class DescriminadoSheet implements FromArray, ShouldAutoSize, WithColumnWidths, WithTitle, WithStyles, WithEvents
{
  public function __construct($start_date, $end_date)
  {
    set_time_limit(240);
    $this->start_date = $start_date;
    $this->end_date = $end_date;

    $this->range = '';
    $this->last_it = '';
    $this->borders = array();
    $this->merger = array();
    $this->in_merger = array();
    $this->HEADERS = array();
    $this->lc_merge = array();
    $this->total_merger = array();
  }

  public function registerEvents(): array
  {
      return [
          AfterSheet::class => function(AfterSheet $event) {
            foreach ($this->borders as $key => $border_range) {
              $event->sheet->getStyle($border_range)->applyFromArray([
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
            }
            $HEAD_RANGE = 'A3:I4';

            $event->sheet->getStyle($HEAD_RANGE)->applyFromArray([
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
          },
      ];
  }

  public function title(): string
  {
    return "Detalhado";
  }

  public function columnWidths(): array
  {
      return [
          'A' => 45,
          'B' => 30,
          'C' => 17,
          'D' => 17,
          'E' => 17,
          'F' => 17,
          'G' => 17,
          'H' => 17,
          'I' => 17,
      ];
  }

  public function styles(Worksheet $sheet)
  {
    $this->range = $sheet->calculateWorksheetDimension();

    $sheet->getStyle('A1:H1')
          ->getFont()
          ->setBold(false)
          ->setSize(11);

      $sheet->getRowDimension('1')->setRowHeight(17);

      $sheet->mergeCells('D3:E3')
            ->mergeCells('F3:G3')
            ->mergeCells('H3:I3')
            ->mergeCells('A3:A4')
            ->mergeCells('C3:C4')
            ->mergeCells('B3:B4');


      $cells = 'A'.$this->last_it.':B'.$this->last_it;
      $sheet->mergeCells($cells);
      $sheet->getRowDimension($this->last_it)->setRowHeight(20);

      $sheet->getStyle('C4:H4')
            ->getFont()
            ->setSize(10);

      $sheet->getStyle($this->range)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

    for ($i=3; $i < $this->last_it; $i++) {
      $sheet->getRowDimension($i)->setRowHeight(20);
    }

    foreach ($this->HEADERS as $key => $mrg) {
      $sheet->getStyle($mrg)
           ->getFill()
           ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
           ->getStartColor()
           ->setARGB('755874');
       $sheet->getStyle($mrg)
             ->getFont()
             ->getColor()
             ->setARGB('FFFFFF');
    }

    foreach ($this->merger as $key => $mrg) {
      $cells = 'A'.$mrg[0].':A'.$mrg[1];
      $sheet->mergeCells($cells);
      $sheet->getStyle($cells)
           ->getFill()
           ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
           ->getStartColor()
           ->setARGB('BFBFBF');
    }

    foreach ($this->lc_merge as $key => $mrg) {
      $cells = 'B'.$mrg[0].':B'.$mrg[1];
      $sheet->mergeCells($cells);
      $sheet->getStyle($cells)
           ->getFill()
           ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
           ->getStartColor()
           ->setARGB('D1D1D1');
    }

    foreach ($this->in_merger as $key => $mrg) {
      $sheet->mergeCells($mrg);
    }

    foreach ($this->total_merger as $key => $m_rage) {
      $sheet->mergeCells($m_rage);
      $sheet->getStyle($m_rage)
           ->getFill()
           ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
           ->getStartColor()
           ->setARGB('BFBFBF');
    }

  }

  public function array() : array
  {
    return $this->GetData();
  }


  public function getTitle(){
    $info = array();
    $info[0][0] = 'Periodo de';
    $info[0][1] = $this->start_date;
    $info[0][2] = 'até';
    $info[0][3] = $this->end_date;
    return $info;
  }


  public function getFormat($it){
    $info[$it][0] = '';
    $it++;
    $info[$it][0] = 'UEO';
    $info[$it][1] = 'Local';
    $info[$it][2] = 'Cat.';
    $info[$it][3] = '1ºRef';
    $info[$it][4] = '';
    $info[$it][5] = '2ºRef';
    $info[$it][6] = '';
    $info[$it][7] = '3ºRef';
    $info[$it][8] = '';
    $it++;
    $info[$it][0] = '';
    $info[$it][1] = '';
    $info[$it][2] = '';
    $info[$it][3] = 'Marcadas';
    $info[$it][4] = 'Consumidas';
    $info[$it][5] = 'Marcadas';
    $info[$it][6] = 'Consumidas';
    $info[$it][7] = 'Marcadas';
    $info[$it][8] = 'Consumidas';
    $it++;
    return $info;
  }

  public function GetTags($REF, $CIVIL, $UNIT, $LOCAL){

    $TAGS =  marcacaotable::where('meal', $REF)
                ->where('civil', $CIVIL)
                ->where('unidade', $UNIT)
                ->where('local_ref', $LOCAL)
                ->where('data_marcacao', '>=', $this->start_date)
                ->where('data_marcacao', '<=', $this->end_date)
                ->count();
    return ($TAGS == 0) ? '0' : $TAGS;
  }

  public function GetConsum($REF, $CIVIL, $UNIT, $LOCAL){
    $TAGS =  entradasQuiosque::where('REF', $REF)
                ->where('CIVIL', $CIVIL)
                ->where('UNIDADE', $UNIT)
                ->where('LOCAL', $LOCAL)
                ->where('REGISTADO_DATE', '>=', $this->start_date)
                ->where('REGISTADO_DATE', '<=', $this->end_date)
                ->count();
    return ($TAGS == 0) ? '0' : $TAGS;
  }

  public function GetData(){
    $info = $this->getTitle();
    $it = 3;
    $it_m = 0;
    $it_m_2 = 0;

    $UNITS = unap_unidades::get()->all();
    $LOCAIS =  locaisref::where('status', 'OK')->get()->all();

    $_1REF_MAR_ALL = 0;
    $_2REF_MAR_ALL = 0;
    $_3REF_MAR_ALL = 0;

    $_1REF_CONSUM_ALL = 0;
    $_2REF_CONSUM_ALL = 0;
    $_3REF_CONSUM_ALL = 0;

    foreach ($UNITS as $key => $_UNIT) {
      $info = $info + $this->getFormat($it);
      $this->HEADERS[] = 'A'.$it.':I'.($it+1);
      $this->in_merger[] = 'D'.$it.':E'.$it;
      $this->in_merger[] = 'F'.$it.':G'.$it;
      $this->in_merger[] = 'H'.$it.':I'.$it;
      $this->in_merger[] = 'A'.$it.':A'.($it+1);
      $this->in_merger[] = 'B'.$it.':B'.($it+1);
      $this->in_merger[] = 'C'.$it.':C'.($it+1);
      $it += 2;
      $it_temp = $it;

      $total_1ref_marca = 0;
      $total_1ref_consum = 0;

      $total_2ref_marca = 0;
      $total_2ref_consum = 0;

      $total_3ref_marca = 0;
      $total_3ref_consum = 0;

      foreach ($LOCAIS as $key => $_LOCAL) {
          $it_temp_2 = $it;
          $it++;

          $_1REF_MAR_MIL =  $this->GetTags('1REF', 'N', $_UNIT['slug'], $_LOCAL['refName']);
          $_2REF_MAR_MIL =  $this->GetTags('2REF', 'N', $_UNIT['slug'], $_LOCAL['refName']);
          $_3REF_MAR_MIL =  $this->GetTags('3REF', 'N', $_UNIT['slug'], $_LOCAL['refName']);
          $_1REF_MAR_CIV =  $this->GetTags('1REF', 'Y', $_UNIT['slug'], $_LOCAL['refName']);
          $_2REF_MAR_CIV =  $this->GetTags('2REF', 'Y', $_UNIT['slug'], $_LOCAL['refName']);
          $_3REF_MAR_CIV =  $this->GetTags('3REF', 'Y', $_UNIT['slug'], $_LOCAL['refName']);

          $_1REF_CONSUM_MIL =  $this->GetConsum('1REF', 'N', $_UNIT['slug'], $_LOCAL['refName']);
          $_2REF_CONSUM_MIL =  $this->GetConsum('2REF', 'N', $_UNIT['slug'], $_LOCAL['refName']);
          $_3REF_CONSUM_MIL =  $this->GetConsum('3REF', 'N', $_UNIT['slug'], $_LOCAL['refName']);
          $_1REF_CONSUM_CIV =  $this->GetConsum('1REF', 'Y', $_UNIT['slug'], $_LOCAL['refName']);
          $_2REF_CONSUM_CIV =  $this->GetConsum('2REF', 'Y', $_UNIT['slug'], $_LOCAL['refName']);
          $_3REF_CONSUM_CIV =  $this->GetConsum('3REF', 'Y', $_UNIT['slug'], $_LOCAL['refName']);

          $_1REF_MAR_ALL += ($_1REF_MAR_MIL + $_1REF_MAR_CIV);
          $_2REF_MAR_ALL += ($_2REF_MAR_MIL + $_2REF_MAR_CIV);
          $_3REF_MAR_ALL += ($_3REF_MAR_MIL + $_3REF_MAR_CIV);

          $_1REF_CONSUM_ALL += ($_1REF_CONSUM_MIL + $_1REF_CONSUM_CIV);
          $_2REF_CONSUM_ALL += ($_2REF_CONSUM_MIL + $_2REF_CONSUM_CIV);
          $_3REF_CONSUM_ALL += ($_3REF_CONSUM_MIL + $_3REF_CONSUM_CIV);

          $TOTAL_MAR_MIL = ($_1REF_MAR_MIL + $_2REF_MAR_MIL + $_3REF_MAR_MIL);
          $TOTAL_CONS_MIL = ($_1REF_CONSUM_MIL + $_2REF_CONSUM_MIL + $_3REF_CONSUM_MIL);

          $TOTAL_MAR_CIV = ($_1REF_MAR_CIV + $_2REF_MAR_CIV + $_3REF_MAR_CIV);
          $TOTAL_CONS_CIV = ($_1REF_CONSUM_CIV + $_2REF_CONSUM_CIV + $_3REF_CONSUM_CIV);

          $info[$it][0] = $_UNIT['name'];
          $info[$it][1] = $_LOCAL['localName'];
          $info[$it][2] = 'Militares';
          $info[$it][3] = $_1REF_MAR_MIL;
          $info[$it][4] = $_1REF_CONSUM_MIL;
          $info[$it][5] = $_2REF_MAR_MIL;
          $info[$it][6] = $_2REF_CONSUM_MIL;
          $info[$it][7] = $_3REF_MAR_MIL;
          $info[$it][8] = $_3REF_CONSUM_MIL;

          $it++;
          $info[$it][0] = $_UNIT['name'];
          $info[$it][1] = $_LOCAL['localName'];
          $info[$it][2] = 'Civis';
          $info[$it][3] = $_1REF_MAR_CIV;
          $info[$it][4] = $_1REF_CONSUM_CIV;
          $info[$it][5] = $_2REF_MAR_CIV;
          $info[$it][6] = $_2REF_CONSUM_CIV;
          $info[$it][7] = $_3REF_MAR_CIV;
          $info[$it][8] = $_3REF_CONSUM_CIV;

          $total_1ref_marca += ($_1REF_MAR_MIL + $_1REF_MAR_CIV);
          $total_1ref_consum += ($_1REF_CONSUM_MIL + $_1REF_CONSUM_CIV);

          $total_2ref_marca += ($_2REF_MAR_MIL + $_2REF_MAR_CIV);
          $total_2ref_consum += ($_2REF_CONSUM_MIL + $_2REF_CONSUM_CIV);

          $total_3ref_marca += ($_3REF_MAR_MIL + $_3REF_MAR_CIV);
          $total_3ref_consum += ($_3REF_CONSUM_MIL + $_3REF_CONSUM_CIV);

          $this->lc_merge[] = [$it_temp_2, ($it-1)];
      }

      $it++;
      $info[$it][0] = $_UNIT['name'];
      $info[$it][1] = 'Total';
      $info[$it][2] = '';
      $info[$it][3] = ($total_1ref_marca != 0) ? $total_1ref_marca : "Nada marcado";
      $info[$it][4] = ($total_1ref_consum != 0) ? $total_1ref_consum : "Nada consumido";
      $info[$it][5] = ($total_2ref_marca != 0) ? $total_2ref_marca : "Nada marcado";
      $info[$it][6] = ($total_2ref_consum != 0) ? $total_2ref_consum : "Nada consumido";
      $info[$it][7] = ($total_3ref_marca != 0) ? $total_3ref_marca : "Nada marcado";
      $info[$it][8] = ($total_3ref_consum != 0) ? $total_3ref_consum : "Nada consumido";

      $this->total_merger[] = 'B'.($it-1).':C'.($it-1);
      $this->merger[] = [$it_temp, ($it-1)];
      $this->borders[] = 'A'.($it_temp-2).':I'.($it-1);

      $it++;
      $info[$it][0] = '';
      $it++;
      $info[$it][0] = '';
    }
    $this->last_it = $it;
    return $info;
  }

}
