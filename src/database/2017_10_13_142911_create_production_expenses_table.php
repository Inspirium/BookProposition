<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_expenses', function (Blueprint $table) {
        	$table->increments('id');
        	$table->integer('proposition_id');
        	$table->string('type')->nullable();
        	$table->integer('parent_id')->nullable();
	        $table->string('text_price')->nullable();
	        $table->string('text_price_amount')->nullable();
	        $table->string('accontation')->nullable();
	        $table->string('netto_price_percentage')->nullable();
	        $table->string('reviews')->nullable();
	        $table->string('lecture')->nullable();
	        $table->string('lecture_amount')->nullable();
	        $table->string('correction')->nullable();
	        $table->string('correction_amount')->nullable();
	        $table->string('proofreading')->nullable();
	        $table->string('proofreading_amount')->nullable();
	        $table->string('translation')->nullable();
	        $table->string('translation_amount')->nullable();
	        $table->string('index')->nullable();
	        $table->string('index_amount')->nullable();
	        $table->string('epilogue')->nullable();
	        $table->string('photos')->nullable();
	        $table->string('photos_amount')->nullable();
	        $table->string('illustrations')->nullable();
	        $table->string('illustrations_amount')->nullable();
	        $table->string('technical_drawings')->nullable();
	        $table->string('technical_drawings_amount')->nullable();
	        $table->string('expert_report')->nullable();
	        $table->string('copyright')->nullable();
	        $table->string('copyright_mediator')->nullable();
	        $table->string('selection')->nullable();
	        $table->string('powerpoint_presentation')->nullable();
	        $table->string('methodical_instrumentarium')->nullable();
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
        Schema::dropIfExists('production_expenses');
    }
}
