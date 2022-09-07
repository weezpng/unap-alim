<?php

namespace App\Exports;

use App\Models\User;
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

class DailyMesses implements FromArray, ShouldAutoSize, WithHeadings,WithColumnWidths,WithStyles,WithTitle,WithEvents
{
  public function __construct($date)
  {
    set_time_limit(240);
    $this->date = $date;
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
        ['NIM / QUARTO', 'NOME', 'POSTO / TIPO', 'UNIDADE', 'PEQUENO-ALMOÇO', '', 'ALMOÇO', '', 'JANTAR', '', ''],
        ['', '', '', '', 'MARCADA', 'CONSUMIDA','MARCADA', 'CONSUMIDA','MARCADA', 'CONSUMIDA'],
     ];
  }

  public function columnWidths(): array
  {
      return [
          'A' => 23,  // NIM
          'B' => 60,  // NOME
          'C' => 23,  // POSTO / TIPO
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
    $all_tags = marcacaotable::where('local_ref', 'like', 'MM%')
                          ->where('data_marcacao', $this->date)
                          ->get();


    $NIMs_TAGS = array();

    foreach ($all_tags as $key => $tag) {
      $NIM = $tag['NIM'];
      while ((strlen((string)$NIM)) < 8) { $NIM = 0 . (string)$NIM; }
      $NIMs_TAGS[] = $NIM;
    }


    $NIMs_TAGS = array_unique($NIMs_TAGS);

    if (empty($NIMs_TAGS)) {
      $info[0][0] = '';
      $info[0][1] = "Nenhuma marcação nesta data (".$this->date.")";
    } else {
      $it = 0;
      foreach ($NIMs_TAGS as $key => $NIM) {
        $u_info = \App\Models\User::where('id', $NIM)->get(['name', 'posto', 'unidade'])->first();
        if ($u_info) {
          $unidade =  \App\Models\unap_unidades::where('slug', $u_info['unidade'])->get('name')->first();
          $info[$it][0] = $NIM;
          $info[$it][1] = strtoupper($u_info['name']);
          $info[$it][2] = (isset($u_info['posto']) && $u_info['posto'] != "") ? $u_info['posto'] : "N/D";
          $info[$it][3] = $unidade['name'];
        } else {
          $u_info = \App\Models\hospede::where('fictio_nim', $NIM)->get()->first();
          if ($u_info) {
            $unidade = \App\Models\unap_unidades::where('slug', $u_info['local'])->get('name')->first();
            $info[$it][0] = 'QUARTO ' . ($u_info['quarto']!="") ? $u_info['quarto'] : "N/D";
            $info[$it][1] = 'HÓSPEDE ' . strtoupper($u_info['name']);
            $info[$it][2] = $u_info['type'] .' '. ($u_info['type_temp']=="PERM") ? "Permanente" : "Temporário";
            $info[$it][3] = $unidade['name'];
          } else {
            $info[$it][0] = $NIM;
            $info[$it][1] = "UTILIZADOR ELIMINADO";
            $info[$it][2] = '';
            $info[$it][3] = '';
          }
        }

        ################### 1º REF ##################################################################################################
        $tag1 = marcacaotable::where('local_ref', 'like', 'MM%')
                            ->where('meal', '1REF')
                            ->where('data_marcacao', $this->date)
                            ->where('NIM', $NIM)->count();
        $ent1 = entradasQuiosque::where('LOCAL', 'like', 'MM%')
                            ->where('REF', '1REF')
                            ->where('REGISTADO_DATE', $this->date)
                            ->where('NIM', $NIM)->count();
        $info[$it][4] = ($tag1 != 0) ? "MARCADA" : 'NÃO';
        $info[$it][5] = ($ent1 != 0) ? "CONSUMIDA" : 'NÃO';

        ################### 2º REF ##################################################################################################
        $tag2 = marcacaotable::where('local_ref', 'like', 'MM%')
                            ->where('meal', '2REF')
                            ->where('data_marcacao', $this->date)
                            ->where('NIM', $NIM)->count();
        $ent2 = entradasQuiosque::where('LOCAL', 'like', 'MM%')
                            ->where('REF', '2REF')
                            ->where('REGISTADO_DATE', $this->date)
                            ->where('NIM', $NIM)->count();
        $info[$it][6] = ($tag2 != 0) ? "MARCADA" : 'NÃO';
        $info[$it][7] = ($ent2 != 0) ? "CONSUMIDA" : 'NÃO';

        ################### 3º REF ##################################################################################################
        $tag3 = marcacaotable::where('local_ref', 'like', 'MM%')
                            ->where('meal', '3REF')
                            ->where('data_marcacao', $this->date)
                            ->where('NIM', $NIM)->count();
        $ent3 = entradasQuiosque::where('LOCAL', 'like', 'MM%')
                            ->where('REF', '3REF')
                            ->where('REGISTADO_DATE', $this->date)
                            ->where('NIM', $NIM)->count();
        $info[$it][8] = ($tag3 != 0) ? "MARCADA" : 'NÃO';
        $info[$it][9] = ($ent3 != 0) ? "CONSUMIDA" : 'NÃO';

        if ($tag3!=0 || $ent3!=0 || $tag2!=0 || $ent2!=0 || $tag1!=0 || $ent1!=0) {
          $it++;
        }
      }
    }

    return $info;

  }

}
