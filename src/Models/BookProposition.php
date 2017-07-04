<?php

namespace Inspirium\BookProposition\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BookProposition
 * @package Inspirium\BookProposition\Models
 *
 * @property $id
 * @property $owner_id
 */
class BookProposition extends Model {
    use SoftDeletes;

    protected $table = 'propositions';

    protected $fillable = [
        'id', 'title',  'concept', 'manuscript', 'dotation', 'dotation_origin', 'dotaion_amount',
        'possible_products', 'supergroup_id', 'upgroup_id',
	    'group_id', 'book_type_group_id', 'book_type_id',
	    'school_type', 'school_level', 'school_assignment',
	    'school_subject_id', 'school_subject_detailed_id',
	    'biblioteca', 'main_target', 'status'
    ];

    protected $casts = [
        'title' => 'string',
        'concept' => 'string',
        'manuscript' => 'string',
        'dotation' => 'boolean',
        'dotation_origin' => 'string',
        'dotation_amount' => 'float',
        'possible_products' => 'array',
	    'supergroup_id' => 'integer',
	    'upgroup_id' => 'integer',
	    'group_id' => 'integer',
	    'book_type_group_id' => 'integer',
	    'book_type_id' => 'integer',
	    'school_type' => 'array',
	    'school_level' => 'array',
	    'school_assignment' => 'boolean',
	    'school_subject_id' => 'integer',
	    'school_subject_detailed_id' => 'integer',
	    'biblioteca' => 'integer',
	    'main_target' => 'string',
	    'status' => 'string',
	    'number_of_pages' => 'integer',
	    'width' => 'string',
	    'height' => 'string',
	    'paper_type' => 'string',
	    'additional_work' => 'string',
	    'colors' => 'string',
	    'colors_first_page' => 'string',
	    'cover_type' => 'string',
	    'cover_paper_type' => 'string',
	    'cover_colors' => 'string',
	    'cover_plastification' => 'string',
	    'film_print' => 'boolean',
	    'blind_print' => 'boolean',
	    'uv_film' => 'boolean',
	    'text_price' => 'string',
	    'text_price_amount' => 'string',
	    'accontation' => 'string',
	    'netto_price_percentage' => 'string',
	    'reviews' => 'string',
	    'lecture' => 'string',
	    'lecture_amount' => 'string',
	    'correction' => 'string',
	    'correction_amount' => 'string',
	    'proofreading' => 'string',
	    'proofreading_amount' => 'string',
	    'translation' => 'string',
	    'translation_amount' => 'string',
	    'index' => 'string',
	    'index_amount' => 'string',
	    'epilogue' => 'string',
	    'photos_amount' => 'string',
	    'illustrations' => 'string',
	    'illustrations_amount' => 'string',
	    'technical_drawings' => 'string',
	    'technical_drawings_amount' => 'string',
	    'export_report' => 'string',
	    'copyright' => 'string',
	    'copyright_mediator' => 'string',
	    'selection' => 'string',
	    'powerpoint_presentation' => 'string',
	    'additional_expense' => 'string',
	    'margin' => 'string',
	    'layout_complexity' => 'string',
	    'layout_include' => 'boolean',
	    'design_complexity' => 'string',
	    'design_include' => 'boolean',
	    'design_note' => 'string',
	    'layout_note' => 'string',
	    'deadline' => 'datetime',
	    'prioriy' => 'string',
    ];

    public function owner() {
    	return $this->belongsTo('Inspirium\UserManagement\Models\User', 'owner_id');
    }

    public function authorExpenses() {
        return $this->hasMany('Inspirium\BookProposition\Models\AuthorExpense', 'proposition_id');
    }

    public function authors() {
        return $this->belongsToMany('Inspirium\BookManagement\Models\Author', 'pivot_proposition_author', 'author_id', 'proposition_id');
    }

	public function notes() {
    	return $this->hasMany('Inspirium\BookProposition\Models\PropositionNote');
	}

	public function getNotesAttribute(){
		return $this->attributes['notes'] = $this->getRelationValue('notes')->keyBy('type');
	}

	public function options() {
		return $this->hasMany( 'Inspirium\BookProposition\Models\PropositionOption', 'proposition_id' );
	}
}
