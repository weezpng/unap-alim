<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocaisrefTable extends Migration
{
    public function up()
    {
        Schema::create('locaisref', function (Blueprint $table) {

      		$table->integer('id')->primary();
      		$table->char('refName',50);
      		$table->text('localName');
      		$table->enum('status',['OK','NOK']);
      		$table->smallInteger('capacity',6);
      		$table->primary('id');

        });
    }

    public function down()
    {
        Schema::dropIfExists('locaisref');
    }
}
