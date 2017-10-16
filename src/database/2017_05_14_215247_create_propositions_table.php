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
            $table->integer('owner_id')->nullable();
            $table->string('project_name')->nullable();
            $table->string('project_number')->nullable();
            $table->string('additional_project_number')->nullable();
            $table->string('title')->nullable();
            $table->string('status')->nullable();
            $table->text('concept')->nullable();
            $table->string('manuscript')->nullable();
            $table->boolean('dotation')->nullable();
            $table->string('dotation_origin')->nullable();
            $table->string('dotation_amount')->nullable();
            $table->text('possible_products')->nullable();

            $table->text('school_level')->nullable();
			$table->boolean('school_assignment')->nullable();

            $table->string('main_target')->nullable();

            $table->text('additions')->nullable();
            $table->text('circulations')->nullable();
            $table->string('number_of_pages')->nullable();
            $table->string('width')->nullable();
            $table->string('height')->nullable();
            $table->string('paper_type')->nullable();
            $table->string('additional_work')->nullable();
            $table->string('colors')->nullable();
            $table->string('colors_first_page')->nullable();
            $table->string('colors_last_page')->nullable();
            $table->string('cover_type')->nullable();
            $table->string('cover_paper_type')->nullable();
            $table->string('cover_colors')->nullable();
            $table->string('cover_plastification')->nullable();
            $table->boolean('film_print')->nullable();
            $table->boolean('blind_print')->nullable();
            $table->boolean('uv_print')->nullable();
            $table->string('book_binding')->nullable();

            $table->datetime('deadline')->nullable();
            $table->string('priority')->nullable();

            $table->text('expenses')->nullable();

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

