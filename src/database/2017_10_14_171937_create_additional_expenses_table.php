<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdditionalExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('additional_expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('expense')->nullable();
            $table->string('amount')->nullable();
            $table->string('type')->nullable();
            $table->integer('connection_id')->nullable();
            $table->string('connection_type')->nullable();
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
        Schema::dropIfExists('additional_expenses');
    }
}
