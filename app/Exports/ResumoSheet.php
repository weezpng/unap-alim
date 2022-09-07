<?php

namespace App\Exports;

use App\Models\locaisref;
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

class ResumoSheet implements FromArray, ShouldAutoSize, WithColumnWidths,WithStyles,WithTitle,WithEvents
{
  public function __construct($start_date, $end_date)
  {
    set_time_limit(240);
    $this->start_date = $start_date;
    $this->end_date = $end_date;

    $this->range = '';
    $this->last_it = '';
    $this->merger = array();
  }

  public function registerEvents(): array
  {
      return [
          AfterSheet::class => function(AfterSheet $event) {
            $_custom_range = str_replace('A1', 'A3', $this->range);
            $event->sheet->getStyle($_custom_range)->applyFromArray([
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
    return "Resumo";
  }

  public function styles(Worksheet $sheet)
  {
      $this->range = $sheet->calculateWorksheetDimension();

      $sheet->getStyle('A1:H1')
            ->getFont()
            ->setBold(false)
            ->setSize(11);

      $sheet->getStyle('A3:H4')
           ->getFill()
           ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
           ->getStartColor()
           ->setARGB('755874');

     $sheet->getStyle('A3:H4')
           ->getFont()
           ->getColor()
           ->setARGB('ffffff');

      $sheet->getRowDimension('1')->setRowHeight(17);

      $sheet->mergeCells('C3:D3')
            ->mergeCells('E3:F3')
            ->mergeCells('G3:H3')
            ->mergeCells('A3:A4')
            ->mergeCells('B3:B4');

      foreach ($this->merger as $key => $_m) {
        $cells = 'A'.$_m[0].':A'.$_m[1];
        $sheet->mergeCells($cells);
      }

      $R ='A5:A'.$this->last_it;

      $sheet->getStyle($R)
           ->getFill()
           ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
           ->getStartColor()
           ->setARGB('D1D1D1');

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

      $B_CELL_RANGE = str_replace('A1:H', 'B5:B', $this->range);

      $sheet->getStyle($B_CELL_RANGE)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_JUSTIFY);
  }

  public function columnWidths(): array
  {
      return [
          'A' => 35,
          'B' => 15,
          'C' => 15,
          'D' => 15,
          'E' => 15,
          'F' => 15,
          'G' => 15,
          'H' => 15,
      ];
  }

  public function array() : array
  {
    return $this->GetData();
  }

  public function GetTags($REF, $CIVIL, $LOCAL){

    $TAGS =  marcacaotable::where('meal', $REF)
                ->where('civil', $CIVIL)
                ->where('local_ref', $LOCAL)
                ->where('data_marcacao', '>=', $this->start_date)
                ->where('data_marcacao', '<=', $this->end_date)
                ->count();
    return ($TAGS == 0) ? '0' : $TAGS;
  }

  public function GetConsum($REF, $CIVIL, $LOCAL){

    $TAGS =  entradasQuiosque::where('REF', $REF)
                ->where('CIVIL', $CIVIL)
                ->where('LOCAL', $LOCAL)
                ->where('REGISTADO_DATE', '>=', $this->start_date)
                ->where('REGISTADO_DATE', '<=', $this->end_date)
                ->count();
    return ($TAGS == 0) ? '0' : $TAGS;
  }

  public function GetQuantTags($MEAL, $MOTIVE){
    $_PEDS = \App\Models\pedidosueoref::where('meal', $MEAL)
            ->where('data_pedido', '>=', $this->start_date)
            ->where('data_pedido', '<=', $this->end_date)
            ->where('motive', 'LIKE', '%' . $MOTIVE . "%")
            ->get();
    $COUNT = 0;
    foreach ($_PEDS as $_KEY => $_PD) {
      $COUNT += $_PD['quantidade'];
    }
    return ($COUNT == 0) ? '0' : $COUNT;
  }

  public function GetQuantConsum($MEAL, $MOTIVE){
    $_PEDS = \App\Models\entradasquiosque::where('REF', $MEAL)
            ->where('REGISTADO_DATE', '>=', $this->start_date)
            ->where('REGISTADO_DATE', '<=', $this->end_date)
            ->where('NIM', $MOTIVE)
            ->get();
    $COUNT = 0;
    foreach ($_PEDS as $_KEY => $_PD) {
      $COUNT += $_PD['QTY'];
    }
    return ($COUNT == 0) ? '0' : $COUNT;
  }

  public function GetQuantDili($MEAL){
    $_PEDS = \App\Models\pedidosueoref::where('meal', $MEAL)
            ->where('data_pedido', '>=', $this->start_date)
            ->where('data_pedido', '<=', $this->end_date)
            ->where('motive', '<>', "DDN")
            ->where('motive', '<>', "PCS")
            ->where('motive', '<>', "(AUTO)PESSOAL DE SERVIÇO")
            ->get();
    $COUNT = 0;
    foreach ($_PEDS as $_KEY => $_PD) {
      $COUNT += $_PD['quantidade'];
    }
    return ($COUNT == 0) ? '0' : $COUNT;
  }

  public function getFormat(){
    $info = array();
    $it = 0;
    $info[$it][0] = 'Periodo de';
    $info[$it][1] = $this->start_date;
    $info[$it][2] = 'até';
    $info[$it][3] = $this->end_date;
    $it++;
    $info[$it][0] = '';
    $it++;
    $info[$it][0] = 'Local';
    $info[$it][1] = 'Cat.';
    $info[$it][2] = '1ºRef';
    $info[$it][3] = '';
    $info[$it][4] = '2ºRef';
    $info[$it][5] = '';
    $info[$it][6] = '3ºRef';
    $info[$it][7] = '';
    $it++;
    $info[$it][0] = '';
    $info[$it][1] = '';
    $info[$it][2] = 'Marcadas';
    $info[$it][3] = 'Consumidas';
    $info[$it][4] = 'Marcadas';
    $info[$it][5] = 'Consumidas';
    $info[$it][6] = 'Marcadas';
    $info[$it][7] = 'Consumidas';
    $it++;
    return $info;
  }

  public function GetData(){
    $info = $this->getFormat();
    $it = 5;
    $it_m = 0;
    $locais = locaisref::where('status', 'OK')->get()->all();

    $_1REF_MAR_ALL = 0;
    $_2REF_MAR_ALL = 0;
    $_3REF_MAR_ALL = 0;

    $_1REF_CONSUM_ALL = 0;
    $_2REF_CONSUM_ALL = 0;
    $_3REF_CONSUM_ALL = 0;

    foreach ($locais as $LC_KEY => $_LC) {

      $_1REF_MAR_MIL =  $this->GetTags('1REF', 'N', $_LC['refName']);
      $_2REF_MAR_MIL =  $this->GetTags('2REF', 'N', $_LC['refName']);
      $_3REF_MAR_MIL =  $this->GetTags('3REF', 'N', $_LC['refName']);
      $_1REF_MAR_CIV =  $this->GetTags('1REF', 'Y', $_LC['refName']);
      $_2REF_MAR_CIV =  $this->GetTags('2REF', 'Y', $_LC['refName']);
      $_3REF_MAR_CIV =  $this->GetTags('3REF', 'Y', $_LC['refName']);

      $_1REF_CONSUM_MIL =  $this->GetConsum('1REF', 'N', $_LC['refName']);
      $_2REF_CONSUM_MIL =  $this->GetConsum('2REF', 'N', $_LC['refName']);
      $_3REF_CONSUM_MIL =  $this->GetConsum('3REF', 'N', $_LC['refName']);
      $_1REF_CONSUM_CIV =  $this->GetConsum('1REF', 'Y', $_LC['refName']);
      $_2REF_CONSUM_CIV =  $this->GetConsum('2REF', 'Y', $_LC['refName']);
      $_3REF_CONSUM_CIV =  $this->GetConsum('3REF', 'Y', $_LC['refName']);

      $_1REF_MAR_ALL += ($_1REF_MAR_MIL + $_1REF_MAR_CIV);
      $_2REF_MAR_ALL += ($_2REF_MAR_MIL + $_2REF_MAR_CIV);
      $_3REF_MAR_ALL += ($_3REF_MAR_MIL + $_3REF_MAR_CIV);

      $_1REF_CONSUM_ALL += ($_1REF_CONSUM_MIL + $_1REF_CONSUM_CIV);
      $_2REF_CONSUM_ALL += ($_2REF_CONSUM_MIL + $_2REF_CONSUM_CIV);
      $_3REF_CONSUM_ALL += ($_3REF_CONSUM_MIL + $_3REF_CONSUM_CIV);

      $info[$it][0] = $_LC['localName'];
      $info[$it][1] = 'Militares';
      $info[$it][2] = $_1REF_MAR_MIL;
      $info[$it][3] = $_1REF_CONSUM_MIL;
      $info[$it][4] = $_2REF_MAR_MIL;
      $info[$it][5] = $_2REF_CONSUM_MIL;
      $info[$it][6] = $_3REF_MAR_MIL;
      $info[$it][7] = $_3REF_CONSUM_MIL;
      $it++;
      $info[$it][0] = '';
      $info[$it][1] = 'Civis';
      $info[$it][2] = $_1REF_MAR_CIV;
      $info[$it][3] = $_1REF_CONSUM_CIV;
      $info[$it][4] = $_2REF_MAR_CIV;
      $info[$it][5] = $_2REF_CONSUM_CIV;
      $info[$it][6] = $_3REF_MAR_CIV;
      $info[$it][7] = $_3REF_CONSUM_CIV;
      $t = $it-1;
      $this->merger[$it_m] = [$t, $it];
      $it++;
      $it_m++;
    }

    $_1REF_MAR_DDN = $this->GetQuantTags('1REF', 'DDN');
    $_2REF_MAR_DDN = $this->GetQuantTags('2REF', 'DDN');
    $_3REF_MAR_DDN = $this->GetQuantTags('3REF', 'DDN');
    $_1REF_MAR_PCS = $this->GetQuantTags('1REF', 'PCS');
    $_2REF_MAR_PCS = $this->GetQuantTags('2REF', 'PCS');
    $_3REF_MAR_PCS = $this->GetQuantTags('3REF', 'PCS');
    $_1REF_MAR_DILI = $this->GetQuantDili('1REF');
    $_2REF_MAR_DILI = $this->GetQuantDili('2REF');
    $_3REF_MAR_DILI = $this->GetQuantDili('3REF');

    $_1REF_CONSUM_DDN = $this->GetQuantConsum('1REF', 'DDN');
    $_2REF_CONSUM_DDN = $this->GetQuantConsum('2REF', 'DDN');
    $_3REF_CONSUM_DDN = $this->GetQuantConsum('3REF', 'DDN');
    $_1REF_CONSUM_PCS = $this->GetQuantConsum('1REF', 'PCS');
    $_2REF_CONSUM_PCS = $this->GetQuantConsum('2REF', 'PCS');
    $_3REF_CONSUM_PCS = $this->GetQuantConsum('3REF', 'PCS');
    $_1REF_CONSUM_DILIG = $this->GetQuantConsum('1REF', 'DILIGENCIA');
    $_2REF_CONSUM_DILIG = $this->GetQuantConsum('2REF', 'DILIGENCIA');
    $_3REF_CONSUM_DILIG = $this->GetQuantConsum('3REF', 'DILIGENCIA');

    $_1REF_MAR_SVC = $this->GetQuantTags('1REF', '(AUTO)PESSOAL DE SERVIÇO');
    $_2REF_MAR_SVC = $this->GetQuantTags('2REF', '(AUTO)PESSOAL DE SERVIÇO');
    $_3REF_MAR_SVC = $this->GetQuantTags('3REF', '(AUTO)PESSOAL DE SERVIÇO');
    $_1REF_CONSUM_SVC = $_1REF_MAR_SVC;
    $_2REF_CONSUM_SVC = $_2REF_MAR_SVC;
    $_3REF_CONSUM_SVC = $_3REF_MAR_SVC;

    $info[$it][0] = 'Quantitativos';
    $info[$it][1] = 'DDN';
    $info[$it][2] = $_1REF_MAR_DDN;
    $info[$it][3] = $_1REF_CONSUM_DDN;
    $info[$it][4] = $_2REF_MAR_DDN;
    $info[$it][5] = $_2REF_CONSUM_DDN;
    $info[$it][6] = $_3REF_MAR_DDN;
    $info[$it][7] = $_3REF_CONSUM_DDN;
    $it++;
    $info[$it][0] = '';
    $info[$it][1] = 'PCS';
    $info[$it][2] = $_1REF_MAR_PCS;
    $info[$it][3] = $_1REF_CONSUM_PCS;
    $info[$it][4] = $_2REF_MAR_PCS;
    $info[$it][5] = $_2REF_CONSUM_PCS;
    $info[$it][6] = $_3REF_MAR_PCS;
    $info[$it][7] = $_3REF_CONSUM_PCS;
    $it++;

    $info[$it][0] = '';
    $info[$it][1] = 'Serviço';
    $info[$it][2] = $_1REF_MAR_SVC;
    $info[$it][3] = $_1REF_CONSUM_SVC;
    $info[$it][4] = $_2REF_MAR_SVC;
    $info[$it][5] = $_2REF_CONSUM_SVC;
    $info[$it][6] = $_3REF_MAR_SVC;
    $info[$it][7] = $_3REF_CONSUM_SVC;
    $it++;

    $info[$it][0] = '';
    $info[$it][1] = 'Diligência';
    $info[$it][2] = $_1REF_MAR_DILI;
    $info[$it][3] = $_1REF_CONSUM_DILIG;
    $info[$it][4] = $_2REF_MAR_DILI;
    $info[$it][5] = $_2REF_CONSUM_DILIG;
    $info[$it][6] = $_3REF_MAR_DILI;
    $info[$it][7] = $_3REF_CONSUM_DILIG;
    $t = $it-3;
    $this->merger[$it_m] = [$t, $it];
    $it++;

    $_1REF_MAR_ALL += ($_1REF_MAR_DDN + $_1REF_MAR_PCS + $_1REF_MAR_DILI + $_1REF_MAR_SVC);
    $_2REF_MAR_ALL += ($_2REF_MAR_DDN + $_2REF_MAR_PCS + $_2REF_MAR_DILI + $_2REF_MAR_SVC);
    $_3REF_MAR_ALL += ($_3REF_MAR_DDN + $_3REF_MAR_PCS + $_3REF_MAR_DILI + $_3REF_MAR_SVC);

    $_1REF_CONSUM_ALL += ($_1REF_CONSUM_DDN + $_1REF_CONSUM_PCS + $_1REF_CONSUM_DILIG + $_1REF_CONSUM_SVC);
    $_2REF_CONSUM_ALL += ($_2REF_CONSUM_DDN + $_2REF_CONSUM_PCS + $_2REF_CONSUM_DILIG + $_2REF_CONSUM_SVC);
    $_3REF_CONSUM_ALL += ($_3REF_CONSUM_DDN + $_3REF_CONSUM_PCS + $_3REF_CONSUM_DILIG + $_3REF_CONSUM_SVC);

    $info[$it][0] = 'Total';
    $info[$it][1] = '';
    $info[$it][2] = ($_1REF_MAR_ALL == 0) ? '0' : $_1REF_MAR_ALL;
    $info[$it][3] = ($_1REF_CONSUM_ALL == 0) ? '0' : $_1REF_CONSUM_ALL;
    $info[$it][4] = ($_2REF_MAR_ALL == 0) ? '0' : $_2REF_MAR_ALL;
    $info[$it][5] = ($_2REF_CONSUM_ALL == 0) ? '0' : $_2REF_CONSUM_ALL;
    $info[$it][6] = ($_3REF_MAR_ALL == 0) ? '0' : $_3REF_MAR_ALL;
    $info[$it][7] = ($_3REF_CONSUM_ALL == 0) ? '0' : $_3REF_CONSUM_ALL;

    $this->last_it = $it;
    return $info;
  }

}
