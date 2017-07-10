<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthorExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('author_expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('proposition_id');
            $table->integer('author_id');
            $table->string('amount');
            $table->string('percentage');
            $table->string('accontation');
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
        Schema::dropIfExists('author_expenses');
    }
}
