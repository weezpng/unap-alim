<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersChildrenSubgroupsTable extends Migration
{
    public function up()
    {
        Schema::create('users_children_subgroups', function (Blueprint $table) {

          $table->tinyInteger('id')->primary();
      		$table->string('groupID',25);
      		$table->string('parentNIM',8);
      		$table->string('parent2nNIM',8)->nullable()->default('NULL');
      		$table->text('groupName');
      		$table->char('groupUnidade',50);
      		$table->enum('groupLocalPrefRef',['QSP','QSO','MMANTAS','MMBATALHA'])->nullable()->default('NULL');
      		$table->timestamp('created_at')->nullable()->default('NULL');
      		$table->timestamp('updated_at')->nullable()->default('NULL');

        });
    }

    public function down()
    {
        Schema::dropIfExists('users_children_subgroups');
    }
}
