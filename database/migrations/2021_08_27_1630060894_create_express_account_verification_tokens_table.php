<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpressAccountVerificationTokensTable extends Migration
{
    public function up()
    {
        Schema::create('express_account_verification_tokens', function (Blueprint $table) {

      		$table->string('token',15)->default('0');
      		$table->string('NIM',8);
      		$table->timestamp('updated_at')->nullable()->default('NULL');
      		$table->timestamp('created_at')->nullable()->default('NULL');
      		$table->string('created_by',15);
      		$table->primary('token');

        });
    }

    public function down()
    {
        Schema::dropIfExists('express_account_verification_tokens');
    }
}
