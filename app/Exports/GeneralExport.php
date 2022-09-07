<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class GeneralExport implements WithMultipleSheets
{

  public function __construct($date)
  {
    set_time_limit(240);
    $this->start_date = $date[0];
    $this->end_date = $date[1];
  }

  public function sheets() : array {
    $sheets = [];
    $sheets[] = new ResumoSheet($this->start_date, $this->end_date);
    $sheets[] = new GeralSheet($this->start_date, $this->end_date);
    $sheets[] = new DescriminadoSheet($this->start_date, $this->end_date);
    return $sheets;
  }
}
