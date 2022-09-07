<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePendingActionsTable extends Migration
{
    public function up()
    {
        Schema::create('pending_actions', function (Blueprint $table) {

      		$table->integer('id')->primary();
      		$table->enum('action_type',['ASSOCIATION']);
      		$table->string('from_id',8);
      		$table->string('to_id',8);
      		$table->enum('is_valid',['Y','N'])->default('Y');
      		$table->integer('notification_id',11)->nullable()->default('NULL');
      		$table->timestamp('created_at')->nullable()->default('NULL');
      		$table->timestamp('updated_at')->nullable()->default('NULL');

        });
    }

    public function down()
    {
        Schema::dropIfExists('pending_actions');
    }
}
