<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnapUnidadesTable extends Migration
{
    public function up()
    {
        Schema::create('unap_unidades', function (Blueprint $table) {

      		$table->integer('id')->primary();
      		$table->char('slug',50)->unique();
      		$table->char('name',50);
      		$table->enum('local',['QSP','QSO','MMANTAS','MMBATALHA']);
      		$table->timestamp('created_at')->nullable()->default('NULL');
      		$table->timestamp('deleted_at')->nullable()->default('NULL');

        });
    }

    public function down()
    {
        Schema::dropIfExists('unap_unidades');
    }
}
