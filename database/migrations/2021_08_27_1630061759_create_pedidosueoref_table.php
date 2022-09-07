<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidosueorefTable extends Migration
{
    public function up()
    {
        Schema::create('pedidosueoref', function (Blueprint $table) {

          $table->integer('id')->primary();
      		$table->enum('local_ref',['QSP','QSO','MMANTAS','MMBATALHA']);
      		$table->date('data_pedido');
      		$table->enum('meal',['1REF','2REF','3REF']);
      		$table->text('motive');
      		$table->integer('quantidade',11);
      		$table->string('registeredByNIM',8);
      		$table->timestamp('created_at')->nullable()->default('NULL');
      		$table->timestamp('updated_at')->nullable()->default('NULL');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pedidosueoref');
    }
}
