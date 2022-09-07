<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFeriasTable extends Migration
{
    public function up()
    {
        Schema::create('user_ferias', function (Blueprint $table) {

      		$table->integer('id')->primary();
      		$table->string('to_user',8);
      		$table->date('data_inicio');
      		$table->date('data_fim');
      		$table->string('registered_by',8);
      		$table->timestamp('created_at')->nullable()->default('NULL');
      		$table->timestamp('deleted_at')->nullable()->default('NULL');
      		$table->timestamp('updated_at')->nullable()->default('NULL');

        });
    }

    public function down()
    {
        Schema::dropIfExists('user_ferias');
    }
}
