<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospedeTable extends Migration
{
    public function up()
    {
        Schema::create('hospede', function (Blueprint $table) {

      		$table->integer('id')->primary();
      		$table->string('fictio_nim',8);
      		$table->text('name');
      		$table->enum('type',['MILITAR','CIVIL']);
      		$table->enum('local',['MMANTAS','MMBATALHA']);
      		$table->string('contacto',9);
      		$table->timestamp('created_at')->nullable()->default('current_timestamp');
      		$table->timestamp('updated_at')->nullable()->default('NULL');
      		$table->timestamp('deleted_at')->nullable()->default('NULL');

        });
    }

    public function down()
    {
        Schema::dropIfExists('hospede');
    }
}
