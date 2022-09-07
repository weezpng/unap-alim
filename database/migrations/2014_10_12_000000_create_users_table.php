<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('user_type',['USER', 'SUPER', 'ADMIN'])->default('USER');
            $table->enum('user_permission',['ALIM','PESS','LOG','MESSES','GCSEL','CCS','GENERAL','TUDO'])->default('GENERAL');
        		$table->enum('posto',['ASS.TEC.','ASS.OP.','TEC.SUP.','SOLDADO','2ºCABO','1ºCABO','CABO-ADJUNTO','2ºFURRIEL','FURRIEL','2ºSARGENTO','1ºSARGENTO','SARGENTO-AJUDANTE','SARGENTO-CHEFE','SARGENTO-MOR','ASPIRANTE','ALFERES','TENENTE','CAPITAO','MAJOR','TENENTE-CORONEL','CORONEL','BRIGADEIRO-GENERAL','MAJOR-GENERAL','TENENTE-GENERAL','GENERAL','MARECHAL'])->nullable()->default('NULL');
        		$table->char('unidade',50);
        		$table->char('trocarUnidade',50)->nullable()->default('NULL');
        		$table->enum('account_verified',['Y','N','COMPLETE'])->default('N');
        		$table->enum('localRefPref',['QSP','QSO','MMANTAS','MMBATALHA'])->nullable()->default('NULL');
        		$table->enum('isAccountChildren',['Y','N','WAITING'])->default('N');
        		$table->string('accountChildrenOf',8)->nullable()->default('NULL');
        		$table->string('account2ndChildrenOf',8)->nullable()->default('NULL');
        		$table->string('accountChildrenGroup',25)->nullable()->default('NULL');
        		$table->string('accountChildrenSubGroup',25)->nullable()->default('NULL');
        		$table->string('accountReplacementPOC',8)->nullable()->default('NULL');
        		$table->string('accountPartnerPOC',8)->nullable()->default('NULL');
        		$table->enum('lock',['Y','N'])->default('N');
        		$table->enum('dark_mode',['Y','N'])->default('N');
        		$table->enum('compact_mode',['Y','N'])->default('N');
        		$table->enum('lite_mode',['Y','N'])->default('N');
        		$table->enum('auto_collapse',['Y','N'])->default('Y');
        		$table->string('isTagOblig',20)->nullable()->default('NULL');
        		$table->string('remember_token',100)->nullable()->default('NULL');
        		$table->timestamp('created_at')->nullable()->default('NULL');
        		$table->string('updated_by',8)->nullable()->default('NULL');
        		$table->timestamp('updated_at')->nullable()->default('NULL');
        		$table->timestamp('verified_at')->nullable()->default('NULL');
        		$table->string('verified_by',8)->nullable()->default('NULL');
        		$table->timestamp('deleted_at')->nullable()->default('NULL');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
