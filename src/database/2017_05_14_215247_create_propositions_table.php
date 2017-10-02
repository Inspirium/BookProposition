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

            $table->integer('supergroup_id')->nullable();
            $table->integer('upgroup_id')->nullable();
            $table->integer('group_id')->nullable();
            $table->integer('book_type_group_id')->nullable();
            $table->integer('book_type_id')->nullable();
            $table->text('school_type')->nullable();
            $table->text('school_level')->nullable();
			$table->boolean('school_assignment')->nullable();
			$table->integer('school_subject_id')->nullable();
			$table->integer('school_subject_detailed_id')->nullable();
			$table->integer('biblioteca_id')->nullable();

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
            $table->string('cover_type')->nullable();
            $table->string('cover_paper_type')->nullable();
            $table->string('cover_colors')->nullable();
            $table->string('cover_plastification')->nullable();
            $table->boolean('film_print')->nullable();
            $table->boolean('blind_print')->nullable();
            $table->boolean('uv_print')->nullable();
            $table->string('book_binding')->nullable();

            $table->text('author_other_expense')->nullable();

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
            $table->text('production_additional_expense')->nullable();

            $table->string('marketing_expense')->nullable();
			$table->text('marketing_additional_expense')->nullable();

            $table->string('margin')->nullable();

            $table->string('layout_complexity')->nullable();
            $table->boolean('layout_include')->nullable();
            $table->string('design_complexity')->nullable();
            $table->boolean('design_include')->nullable();
            $table->text('layout_note')->nullable();
            $table->text('design_note')->nullable();

            $table->datetime('deadline')->nullable();
            $table->string('priority')->nullable();

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

