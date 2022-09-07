<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTaggedConfTable extends Migration
{
    public function up()
    {
        Schema::create('users_tagged_conf', function (Blueprint $table) {

      		$table->string('id',20)->primary();
      		$table->date('data_inicio');
      		$table->date('data_fim');
      		$table->string('registered_to',8);
      		$table->string('registered_by',8);
      		$table->timestamp('updated_at')->nullable()->default('NULL');
      		$table->timestamp('created_at')->nullable()->default('NULL');

        });
    }

    public function down()
    {
        Schema::dropIfExists('users_tagged_conf');
    }
}
