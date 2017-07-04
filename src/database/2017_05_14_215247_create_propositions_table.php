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
            $table->string('title')->nullable();
            $table->string('status')->nullable();
            $table->text('concept')->nullable();
            $table->string('manuscript')->nullable();
            $table->boolean('dotation')->nullable();
            $table->string('dotation_origin')->nullable();
            $table->string('dotation_amount')->nullable();
            $table->string('possible_products')->nullable();

            $table->integer('supergroup_id')->nullable();
            $table->integer('upgroup_id')->nullable();
            $table->integer('group_id')->nullable();
            $table->integer('book_type_group_id')->nullable();
            $table->integer('book_type_id')->nullable();
            $table->string('school_type')->nullable();
            $table->string('school_level')->nullable();
			$table->boolean('school_assignment')->nullable();
			$table->integer('school_subject_id')->nullable();
			$table->integer('school_subject_detailed_id')->nullable();
			$table->integer('biblioteca')->nullable();

            $table->string('main_target')->nullable();

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

