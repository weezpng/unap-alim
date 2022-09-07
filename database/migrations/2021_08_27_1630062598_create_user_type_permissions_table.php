<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTypePermissionsTable extends Migration
{
    public function up()
    {
        Schema::create('user_type_permissions', function (Blueprint $table) {

      		$table->integer('id')->primary();
      		$table->text('permission')->unique());
      		$table->enum('usergroupAdmin',['Y','N'])->default('N');
      		$table->enum('usergroupSuper',['Y','N'])->default('N');
      		$table->enum('usergroupUser',['Y','N'])->default('N');
      		$table->string('updated_by',8)->nullable()->default('NULL');
      		$table->timestamp('updated_at')->nullable()->default('NULL');

        });
    }

    public function down()
    {
        Schema::dropIfExists('user_type_permissions');
    }
}
