<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmentatableTable extends Migration
{
    public function up()
    {
        Schema::create('ementatable', function (Blueprint $table) {

      		$table->integer('id')->primary();
      		$table->date('data');
      		$table->text('sopa_almoço')->default('0');
      		$table->text('prato_almoço')->default('');
      		$table->text('sobremesa_almoço')->default('');
      		$table->text('sopa_jantar')->default('');
      		$table->text('prato_jantar')->default('');
      		$table->text('sobremesa_jantar')->default('');
      		$table->string('created_by',8)->nullable()->default('NULL');
      		$table->string('edited_by',8)->nullable()->default('NULL');
      		$table->timestamp('updated_at')->nullable()->default('NULL');
      		$table->timestamp('created_at')->nullable()->default('NULL');

        });
    }

    public function down()
    {
        Schema::dropIfExists('ementatable');
    }
}
