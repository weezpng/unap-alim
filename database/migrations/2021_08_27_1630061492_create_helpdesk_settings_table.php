<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHelpdeskSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('helpdesk_settings', function (Blueprint $table) {

      		$table->integer('id')->primary();
      		$table->text('settingText')->default('0');
      		$table->enum('settingToggleMode',['BOOLEAN','INT']);
      		$table->enum('settingToggleBoolean',['Y','N'])->nullable()->default('NULL');
      		$table->tinyInteger('settingToggleInt',4)->default('0');
      		$table->string('settingToggleIntLabel',50)->nullable()->default('NULL');
          $table->string('settingToggleBoolLabel',50)->nullable()->default('NULL');
      		$table->timestamp('created_at')->nullable()->default('NULL');
      		$table->timestamp('updated_at')->nullable()->default('NULL');
      		$table->string('updated_by',8)->nullable()->default('NULL');

        });
    }

    public function down()
    {
        Schema::dropIfExists('helpdesk_settings');
    }
}
