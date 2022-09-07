<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationTableTable extends Migration
{
    public function up()
    {
        Schema::create('notification_table', function (Blueprint $table) {

      		$table->integer('id')->primary();
      		$table->text('notification_title');
      		$table->text('notification_text')->default('0');
      		$table->enum('notification_type',['NORMAL','WARNING'])->default('NORMAL');
          $table->set('notification_geral', ['HELPDESK','ADMINS','SUPERS','USERS']);
      		$table->string('notification_toUser',8)->nullable()->default('NULL');
      		$table->enum('notification_seen',['Y','N'])->default('N');
      		$table->timestamp('created_at')->nullable()->default('NULL');
      		$table->timestamp('updated_at')->nullable()->default('NULL');
      		$table->string('created_by',40)->nullable()->default('NULL');
      		$table->date('lapses_at')->nullable()->default('NULL');
      		$table->timestamp('deleted_at')->nullable()->default('NULL');

        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_table');
    }
}
