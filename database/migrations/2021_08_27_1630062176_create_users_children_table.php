<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersChildrenTable extends Migration
{
    public function up()
    {
        Schema::create('users_children', function (Blueprint $table) {

        $table->integer('id')->primary();
        $table->string('parentNIM',8);
    		$table->string('parent2nNIM',8)->nullable()->default('NULL');
    		$table->string('childID',8)->default('');
    		$table->text('childNome')->default('');
    		$table->text('childEmail')->default('');
    		$table->enum('childPosto',['ASS.TEC.','ASS.OP.','TEC.SUP.','SOLDADO','2ºCABO','1ºCABO','CABO-ADJUNTO','2ºFURRIEL','FURRIEL','2ºSARGENTO','1ºSARGENTO','SARGENTO-AJUDANTE','SARGENTO-CHEFE','SARGENTO-MOR','ASPIRANTE','ALFERES','TENENTE','CAPITAO','MAJOR','TENENTE-CORONEL','CORONEL','BRIGADEIRO-GENERAL','MAJOR-GENERAL','TENENTE-GENERAL','GENERAL','MARECHAL']);
    		$table->char('childUnidade',50);
    		$table->char('trocarUnidade',50)->nullable()->default('NULL');
    		$table->text('descriptor')->nullable()->default('NULL');
    		$table->text('seccao')->nullable()->default('NULL');
    		$table->enum('localRefPref',['QSP','QSO','MMANTAS','MMBATALHA'])->default('QSP');
    		$table->enum('accountVerified',['Y','N'])->default('N');
    		$table->string('childGroup',25)->nullable()->default('NULL');
    		$table->string('childSubGroup',25)->nullable()->default('NULL');
    		$table->timestamp('updated_at')->nullable()->default('NULL');
    		$table->timestamp('created_at')->nullable()->default('NULL');

        });
    }

    public function down()
    {
        Schema::dropIfExists('users_children');
    }
}
