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
            $table->integer('proposition_id')->nullable();
            $table->integer('author_id')->nullable();
            $table->string('amount')->nullable();
            $table->string('percentage')->nullable();
            $table->string('accontation')->nullable();
            $table->text('additional_expenses')->nullable();
            $table->string('type')->nullable();
            $table->integer('parent_id')->nullable();
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
