<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHelpdeskPermissionsTable extends Migration
{
    public function up()
    {
        Schema::create('helpdesk_permissions', function (Blueprint $table) {

      		$table->integer('id')->primary();
      		$table->string('permission_slug',25);
      		$table->string('permission_title',50);
      		$table->text('permission_description');
      		$table->set('permission_apply_to', ['ALIM','PESS','LOG','MESSES','GCSEL','CCS']);
      		$table->string('updated_by',8)->nullable()->default('NULL');
      		$table->timestamp('updated_at')->nullable()->default('NULL');

        });
    }

    public function down()
    {
        Schema::dropIfExists('helpdesk_permissions');
    }
}
