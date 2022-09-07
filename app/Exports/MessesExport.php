<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MessesExport implements WithMultipleSheets
{

  public function __construct($date)
  {
    set_time_limit(240);
    $this->date = date($date);
  }

  public function sheets() : array {

    $month = $this->date;
    while ((strlen((string)$month)) < 2) { $month = 0 . (string)$month; }

    $sheets = [];
    $sheets[] = new MonthlyExportMesses($this->date);
    $sheets[] = new AllUsersWithTagsMesses($this->date);

    foreach ($this->dateRange(date('Y-'.$month.'-01',), date('Y-'.$month.'-t')) as $key => $date) {
      $sheets[] = new DailyMesses($date);
    }



    return $sheets;
  }


  /**
   * Gera um array com todas as dentes entre duas datas
   *
   * @param string $first - Primeiro dia
   * @param string $last - Ultimo dia
   *
   * @return array
   */
  function dateRange( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {
      $dates = [];
      $current = strtotime( $first );
      $last = strtotime( $last );

      while( $current <= $last ) {

          $dates[] = date( $format, $current );
          $current = strtotime( $step, $current );
      }

      return $dates;
  }

}
