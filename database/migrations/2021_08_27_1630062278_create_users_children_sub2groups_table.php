<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersChildrenSub2groupsTable extends Migration
{
    public function up()
    {
        Schema::create('users_children_sub2groups', function (Blueprint $table) {

          $table->tinyInteger('id')->primary();
      		$table->string('subgroupID');
      		$table->string('parentGroupID');
      		$table->string('subgroupName');
      		$table->string('parentNIM');
      		$table->string('parent2nNIM')->nullable()->default('NULL');
      		$table->enum('subgroupLocalPref',['QSP','QSO','MMANTAS','MMBATALHA'])->default('QSP');
      		$table->datetime('created_at')->nullable()->default('NULL');
      		$table->datetime('updated_at')->nullable()->default('NULL');

        });
    }

    public function down()
    {
        Schema::dropIfExists('users_children_sub2groups');
    }
}
