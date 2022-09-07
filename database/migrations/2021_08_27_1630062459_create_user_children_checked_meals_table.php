<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserChildrenCheckedMealsTable extends Migration
{
    public function up()
    {
        Schema::create('user_children_checked_meals', function (Blueprint $table) {

      		$table->integer('id')->primary();
      		$table->date('data');
      		$table->enum('ref',['1REF','2REF','3REF']);
      		$table->enum('check',['Y','N']);
      		$table->string('user',8);
      		$table->timestamp('created_at')->nullable()->default('NULL');
      		$table->timestamp('updated_at')->nullable()->default('NULL');

        });
    }

    public function down()
    {
        Schema::dropIfExists('user_children_checked_meals');
    }
}
