<?php

namespace App\Exports;

use App\Models\User;
use App\Models\locaisref;
use App\Models\pedidosueoref;
use App\Models\entradasQuiosque;
use App\Models\unap_unidades;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PedidosQuantExport implements FromArray, ShouldAutoSize, WithHeadings,WithColumnWidths,WithStyles,WithTitle,WithEvents
{
  public function __construct($date)
  {
    set_time_limit(240);
    $this->date = $date;
    $this->LAST_LINE = (locaisRef::count());
    $this->HEADER_LINE = 'A1:I1';
    $this->SECOND_HEADER_LINE = 'G2:H2';
    $this->range = '';

    $this->refs = array(
      '1REF',
      '2REF',
      '3REF'
    );
  }

  public function registerEvents(): array
  {
    return [
        AfterSheet::class => function(AfterSheet $event) {
            $event->sheet->getDelegate()->freezePane('A2');
            $event->sheet->getDelegate()->freezePane('A3');
        },
        AfterSheet::class    => function(AfterSheet $event) {
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
        },
        AfterSheet::class => function(AfterSheet $event) {
          $highestRow = $event->sheet->getHighestRow(); // e.g. 10
          $highestCol = $event->sheet->getHighestColumn();
          $val = 'A'.$highestRow;
          for ($i=0; $i <= $highestRow; $i++) {
            $cell = 'A'.$i;
            if ($event->sheet->getDelegate()->getCell($cell)->getValue() == "TOTAL") {
              $range = 'A'.$i.':'.$highestCol.$i;
              $event->sheet->getStyle($range)->applyFromArray([
                  'font' => [
                      'bold' => true
                  ],
                  'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' =>'E0E0E0']
                ]
              ]);
             }
          }
        },
    ];
  }

  public function title(): string
  {
    $mes  = array("Janeiro","Fevereiro","Março","Abril", "Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
    $semana  = array("Segunda-Feira", "Terça-Feira","Quarta-Feira", "Quinta-Feira","Sexta-Feira","Sábado", "Domingo");
    $mes_index = date('m', strtotime($this->date));
    $weekday_number = date('N',  strtotime($this->date));
    return date('d', strtotime($this->date)).' '.$mes[($mes_index - 1)].' '.date('Y', strtotime($this->date));
  }

  public function headings(): array
  {
    return [
       ['PEDIDO POR', 'MOTIVO', 'CLASSIFICADO COMO', 'LOCAL', 'UNIDADE', 'REFEIÇÃO', 'NUMEROS', '', 'REFORÇOS'],
       ['', '', '', '', '', '', 'QUANTIDADE', 'CONSUMIDOS', ''],
    ];
  }

  public function columnWidths(): array
  {
     return [
         'A' => 60,  // 0 - PEDIDO POR
         'B' => 45,  // 1 - MOTIVO
         'C' => 45,  // 1 - MOTIVO
         'D' => 23,  // 2 - LOCAL
         'E' => 44,  // 3 - UNIDADE
         'F' => 20,  // 4 - REFEIÇÃO
         'G' => 17,  // 5 - STATS `
         'H' => 20,  // 6 - STATS -> MERGE
         'I' => 20,  // 7 - REFORÇOS
     ];
    }

    public function styles(Worksheet $sheet)
    {



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

    $sheet->getStyle('A:I')
         ->getAlignment()
         ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
         ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

    $sheet->mergeCells('G1:H1');

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
    $it = 0;

    $last_NIM = '';
    $last_LOCAL;
    $last_MEAL;

    $count_NIM = 0;
    $count = 0;
    $count_refç = 0;

    $info = array();

    $info[$it][0] = '';
    $info[$it][1] = '';
    $info[$it][2] = '';
    $info[$it][3] = '';
    $info[$it][4] = '';
    $info[$it][5] = '';
    $info[$it][6] = '';
    $info[$it][7] = '';
    $info[$it][8] = '';
    $it++;

    foreach ($this->refs as $_REFs_key => $_REF) {
      $pedidos = \App\Models\pedidosueoref::where('data_pedido', $this->date)->where('meal', $_REF)->orderBy('registeredByNIM')->orderBy('motive')->orderBy('local_ref')->get();

      $QTY_DDN = array();
      $QTY_PCS = array();
      $QTY_DILI = array();

      $QTY_DDN[0] = 0;
      $QTY_DDN[1] = 0;

      $QTY_PCS[0] = 0;
      $QTY_PCS[1] = 0;

      $QTY_DILI[0] = 0;
      $QTY_DILI[1] = 0;

      $QTY_OTHERS[0] = 0;
      $QTY_OTHERS[1] = 0;


      $MEAL = '';

      foreach ($pedidos as $_PEDs_key => $_PED) {
        $PED_BY = $_PED['registeredByNIM'];
        $BY_DESCRIPTOR = '';
        $MOTIVE_CLASS = '';
        if (str_contains($PED_BY,'@System')) {
          $BY_DESCRIPTOR = "PEDIDO AUTOMÁTICO";
          $MOTIVE_CLASS = 'SERVIÇO';
        } else {
          while ((strlen((string)$PED_BY)) < 8) { $PED_BY = 0 . (string)$PED_BY; }
          $REQ_BY_USER = User::where('id', $PED_BY)->first();
          if ($REQ_BY_USER['user_permission']=="GCSEL") $MOTIVE_CLASS = "PCS";
          elseif (str_contains(strtoupper($_PED['motive']), 'DDN')) $MOTIVE_CLASS = 'DDN';
          else $MOTIVE_CLASS = 'DILIGENCIA';

          $BY_DESCRIPTOR = $PED_BY.' '.$REQ_BY_USER['posto'].' '.$REQ_BY_USER['name'];
          $LOCAL_NAME = \App\Models\locaisref::where('refName', $_PED['local_ref'])->first();
          $LOCAL_NAME = $LOCAL_NAME['localName'];
          $UNIDADE_NAME = \App\Models\unap_unidades::where('slug', $REQ_BY_USER['unidade'])->first();
          $UNIDADE_NAME = $UNIDADE_NAME['name'];


          if ($_REF=='1REF') $MEAL = "Pequeno-almoço";
          elseif ($_REF=='2REF') $MEAL = "Almoço";
          elseif ($_REF=='3REF') $MEAL = "Jantar";

          $QTY_REFORÇOS = ($_PED['qty_reforços']!=NULL) ? $_PED['qty_reforços'] : "Não pedido";

          if ($MOTIVE_CLASS == "DDN") {
            $QTY_DDN[0] += $_PED['quantidade'];
            $QTY_DDN[1] += $_PED['qty_reforços'];
          } elseif ($MOTIVE_CLASS == "PCS") {
            $QTY_PCS[0] += $_PED['quantidade'];
            $QTY_PCS[1] += $_PED['qty_reforços'];
          } else{
            $QTY_DILI[0] += $_PED['quantidade'];
            $QTY_DILI[1] += $_PED['qty_reforços'];
          }

          $info[$it][0] = $BY_DESCRIPTOR;
          $info[$it][1] = $_PED['motive'];
          $info[$it][2] = $MOTIVE_CLASS;
          $info[$it][3] = $LOCAL_NAME;
          $info[$it][4] = $UNIDADE_NAME;
          $info[$it][5] = $MEAL;
          $info[$it][6] = $_PED['quantidade'];
          $info[$it][7] = '-';
          $info[$it][8] = $QTY_REFORÇOS;

          $it++;

        }
      }
      $info[$it][0] = '';
      $info[$it][1] = '';
      $info[$it][2] = '';
      $info[$it][3] = '';
      $info[$it][4] = '';
      $info[$it][5] = '';
      $info[$it][6] = '';
      $info[$it][7] = '';
      $info[$it][8] = '';
      $it++;

      // TOTAL :

      $count = 0;
      $PCS_CONS = \App\Models\entradasQuiosque::where('NIM', "PCS")->where('REGISTADO_DATE', $this->date)->where('REF', $_REF)->get();
      foreach ($PCS_CONS as $key => $_ENTRY) {
        $count += $_ENTRY['QTY'];
      }

      $info[$it][0] = 'TOTAL';
      $info[$it][1] = "PCS";
      $info[$it][2] = "PCS";
      $info[$it][3] = "TODOS";
      $info[$it][4] = "TODAS";
      $info[$it][5] = $MEAL;
      $info[$it][6] = ($QTY_PCS[0]!=0) ? $QTY_PCS[0] : "0";
      $info[$it][7] = ($count!=0) ? $count : "0";
      $info[$it][8] = ($QTY_PCS[1]!=0) ? $QTY_PCS[1] : "0";
      $it++;


      $count = 0;
      $DDN_CONS = \App\Models\entradasQuiosque::where('NIM', "DDN")->where('REGISTADO_DATE', $this->date)->where('REF', $_REF)->get();
      foreach ($DDN_CONS as $key => $_ENTRY) {
        $count += $_ENTRY['QTY'];
      }

      $info[$it][0] = 'TOTAL';
      $info[$it][1] = "DDN";
      $info[$it][2] = "DDN";
      $info[$it][3] = "TODOS";
      $info[$it][4] = "TODAS";
      $info[$it][5] = $MEAL;
      $info[$it][6] = ($QTY_DDN[0]!=0) ? $QTY_DDN[0] : "0";
      $info[$it][7] = ($count!=0) ? $count : "0";
      $info[$it][8] = ($QTY_DDN[1]!=0) ? $QTY_DDN[1] : "0";
      $it++;


      $count = 0;
      $DILI_CONS = \App\Models\entradasQuiosque::where('NIM', "DILIGENCIA")->where('REGISTADO_DATE', $this->date)->where('REF', $_REF)->get();
      foreach ($DILI_CONS as $key => $_ENTRY) {
        $count += $_ENTRY['QTY'];
      }

      $info[$it][0] = 'TOTAL';
      $info[$it][1] = "DILIGÊNCIAS";
      $info[$it][2] = "DILIGÊNCIAS";
      $info[$it][3] = "TODOS";
      $info[$it][4] = "TODAS";
      $info[$it][5] = $MEAL;
      $info[$it][6] = ($QTY_DILI[0]!=0) ? $QTY_DILI[0] : "0";
      $info[$it][7] = ($count!=0) ? $count : "0";
      $info[$it][8] = ($QTY_DILI[1]!=0) ? $QTY_DILI[1] : "0";
      $it++;


      $info[$it][0] = '';
      $info[$it][1] = '';
      $info[$it][2] = '';
      $info[$it][3] = '';
      $info[$it][4] = '';
      $info[$it][5] = '';
      $info[$it][6] = '';
      $info[$it][7] = '';
      $info[$it][8] = '';
      $it++;

    }
    return $info;

  }

}
