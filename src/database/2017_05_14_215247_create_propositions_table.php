<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('propositions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('owner_id');
            $table->string('title');
            $table->string('status');
            $table->string('step');
            $table->text('concept');
            $table->string('manuscript');
            $table->boolean('dotation');
            $table->string('dotation_origin');
            $table->string('dotation_amount');
            $table->text('basic_data_note');
            $table->string('possible_products');

            $table->string('market_main_target');
            $table->text('market_note');

            $table->text('circulation');
            $table->text('additions');
            $table->string('number_of_pages');
            $table->string('width');
            $table->string('height');
            $table->string('paper_type');
            $table->text('colors');
            $table->text('colors_first_page');
            $table->text('colors_last_page');
            $table->string('additional_work');
            $table->string('cover_type');
            $table->string('cover_paper_type');
            $table->text('cover_colors');
            $table->string('cover_plastification');
            $table->boolean('cover_film_print');
            $table->boolean('cover_blind_print');
            $table->boolean('cover_uv_print');
            $table->text('technical_note');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('propositions');
    }
}

