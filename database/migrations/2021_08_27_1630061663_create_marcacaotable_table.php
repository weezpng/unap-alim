<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarcacaotableTable extends Migration
{
    public function up()
    {
        Schema::create('marcacaotable', function (Blueprint $table) {

      		$table->integer('id')->primary();
      		$table->string('NIM',8)->default('0');
      		$table->date('data_marcacao');
      		$table->enum('meal',['1REF','3REF','2REF'])->default('3REF');
      		$table->enum('local_ref',['QSP','QSO','MMANTAS','MMBATALHA'])->default('QSP');
      		$table->string('created_by',50)->nullable()->default('NULL');
      		$table->datetime('updated_at')->nullable()->default('NULL');
      		$table->datetime('created_at')->nullable()->default('NULL');
      		$table->datetime('deleted_at')->nullable()->default('NULL');

        });
    }

    public function down()
    {
        Schema::dropIfExists('marcacaotable');
    }
}
