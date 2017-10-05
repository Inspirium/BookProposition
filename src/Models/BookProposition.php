<?php

namespace Inspirium\BookProposition\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Inspirium\FileManagement\Models\File;

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

    protected $guarded = [];

    protected $dates = [
    	'deadline',
	    'created_at',
	    'updated_at',
	    'deleted_at'
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
	    'additions' => 'array',
	    'circulations' => 'array',
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
	    'uv_print' => 'boolean',
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
	    'methodical_instrumentarium' => 'string',
	    'margin' => 'string',
	    'layout_complexity' => 'string',
	    'layout_include' => 'boolean',
	    'design_complexity' => 'string',
	    'design_include' => 'boolean',
	    'design_note' => 'string',
	    'layout_note' => 'string',
	    'deadline' => 'string',
	    'prioriy' => 'string',
	    'author_other_expense' => 'array',
	    'production_additional_expense' => 'array',
	    'marketing_expense' => 'float',
	    'marketing_additional_expense' => 'array',
	    'expenses' => 'array'
    ];

    public function owner() {
    	return $this->belongsTo('Inspirium\HumanResources\Models\Employee', 'owner_id');
    }

    public function authorExpenses() {
        return $this->hasMany('Inspirium\BookProposition\Models\AuthorExpense', 'proposition_id');
    }

	public function getAuthorExpensesAttribute(){
		return $this->attributes['author_expenses'] = $this->getRelationValue('authorExpenses')->keyBy('author_id');
	}

	public function authors() {
        return $this->belongsToMany('Inspirium\BookManagement\Models\Author', 'pivot_proposition_author', 'proposition_id', 'author_id');
    }

	public function notes() {
    	return $this->hasMany('Inspirium\BookProposition\Models\PropositionNote', 'proposition_id');
	}

	public function getNotesAttribute(){
		return $this->attributes['notes'] = $this->getRelationValue('notes')->keyBy('type');
	}

	public function options() {
		return $this->hasMany( 'Inspirium\BookProposition\Models\PropositionOption', 'proposition_id' );
	}

	public function supergroup() {
		return $this->belongsTo('Inspirium\BookManagement\Models\BookCategory', 'supergroup_id');
	}

	public function upgroup() {
		return $this->belongsTo('Inspirium\BookManagement\Models\BookCategory', 'upgroup_id');
	}

	public function group() {
		return $this->belongsTo('Inspirium\BookManagement\Models\BookCategory', 'group_id');
	}

	public function biblioteca() {
		return $this->belongsTo('Inspirium\BookManagement\Models\BookBiblioteca', 'biblioteca_id');
	}

	public function book_type() {
		return $this->belongsTo('Inspirium\BookManagement\Models\BookType', 'book_type_id');
	}

	public function book_type_group() {
		return $this->belongsTo('Inspirium\BookManagement\Models\BookTypeGroup', 'book_type_group_id');
	}

	public function school_subject() {
		return $this->belongsTo('Inspirium\BookManagement\Models\SchoolSubjectGroup', 'school_subject_id');
	}

	public function school_subject_detailed() {
		return $this->belongsTo('Inspirium\BookManagement\Models\SchoolSubject', 'school_subject_detailed_id');
	}

	public function school_type() {
		return $this->belongsToMany('Inpirium\BookManagement\Models\SchoolType', '', 'school_type_id');
	}

	public function documents() {
		return $this->morphToMany('Inspirium\FileManagement\Models\File', 'fileable')->withPivot('type');
	}

	//attributes
	public function getDotationAttribute($value) {
		if ($value) {
			return 'yes';
		}
		return 'no';
	}

	public function setDotationAttribute($value) {
		$this->attributes['dotation'] = $value==='yes'?1:0;
	}

	public function getSchoolAssignmentAttribute($value) {
		if ($value) {
			return 'yes';
		}
		return 'no';
	}

	public function setSchoolAssignmentAttribute($value) {
		$this->attributes['school_assignment'] = $value==='yes'?1:0;
	}

	public function getFilmPrintAttribute($value) {
		if ($value) {
			return 'yes';
		}
		return 'no';
	}

	public function setFilmPrintAttribute($value) {
		$this->attributes['film_print'] = $value==='yes'?1:0;
	}

	public function getBlindPrintAttribute($value) {
		if ($value) {
			return 'yes';
		}
		return 'no';
	}

	public function setBlindPrintAttribute($value) {
		$this->attributes['blind_print'] = $value==='yes'?1:0;
	}

	public function getUvPrintAttribute($value) {
		if ($value) {
			return 'yes';
		}
		return 'no';
	}

	public function setUvPrintAttribute($value) {
		$this->attributes['uv_print'] = $value==='yes'?1:0;
	}

	public function getLayoutIncludeAttribute($value) {
		if ($value) {
			return 'yes';
		}
		return 'no';
	}

	public function setLayoutIncludeAttribute($value) {
		$this->attributes['layout_include'] = $value==='yes'?1:0;
	}

	public function getDesignIncludeAttribute($value) {
		if ($value) {
			return 'yes';
		}
		return 'no';
	}

	public function setDesignIncludeAttribute($value) {
		$this->attributes['design_include'] = $value==='yes'?1:0;
	}

	public function getSchoolLevelAttribute($value) {
		if (!$value) {
			return [];
		}
		return json_decode($value, true);
	}

	public function getSchoolTypeAttribute($value) {
		if (!$value) {
			return [];
		}
		return json_decode($value, true);
	}

	public function getPossibleProductsAttribute($value) {
		if (!$value) {
			return [];
		}
		return json_decode($value, true);
	}

	public function getAdditionsAttribute($value) {
		if (!$value) {
			return [];
		}
		return json_decode($value, true);
	}

	public function getCirculationsAttribute($value) {
		$out = [];
		foreach ($this->getRelationValue('options') as $option) {
			$out[] = [
				'title' => $option->title,
				'id' => $option->id
			];
		}
		return $out;

		if (!$value) {
			return [];
		}
		return json_decode($value, true);
	}

	public function getOffersAttribute() {
		return $this->attributes['offers'] = $this->getRelationValue('options')->keyBy('id');
	}

	public function getDeadlineAttribute($value) {
		if (!$value) {
			return '';
		}
		return date('d. m. Y.', strtotime($value));
	}

	public function setDeadlineAttribute($value) {
		if (!$value) {
			$this->attributes['deadline'] = null;
		}
		else {
			$date = Carbon::createFromFormat( 'd. m. Y.', $value );
			$date->setTime( 0, 0, 0);
			$this->attributes['deadline'] = $date->toDateTimeString();
		}
	}

	public function getProductionAdditionalExpenseAttribute($value) {
		if (!$value) {
			return [];
		}
		return json_decode($value, true);
	}

	public function getMarketingAdditionalExpenseAttribute($value) {
		if (!$value) {
			return [];
		}
		return json_decode($value, true);
	}

	public function getAuthorOtherExpenseAttribute($value) {
		if (!$value) {
			return [];
		}
		return json_decode($value, true);
	}

	public function getExpensesAttribute($value){
		if ($value) {
			return $value;
		}
		return [
			'text_price' => 0,
			'reviews' => 0,
			'lecture' => 0,
			'correction' => 0,
			'proofreading' => 0,
			'translation' => 0,
			'index' => 0,
			'epilogue' => 0,
			'photos' => 0,
			'illustrations' => 0,
			'expert_report' => 0,
			'copyright' => 0,
			'copyright_mediator' => 0,
			'methodical_instrumentarium' => 0,
			'selection' => 0,
			'powerpoint_presentation' => 0,
			'additional_expense' => 0,
			'marketing_expense' => 0,
		];
	}


	public function getCategorizationAttribute() {
		return [
			'supergroup' => $this->getRelationValue('supergroup'),
			'upgroup' => $this->getRelationValue('upgroup'),
			'group' => $this->getRelationValue('group'),
			'book_type_group' => $this->getRelationValue('book_type_group'),
			'book_type' => $this->getRelationValue('book_type'),
			'school_type' => $this->getRelationValue('school_type'),
			'school_level' => $this->attributes['school_level'],
			'school_assignment' => $this->attributes['school_assignment'],
			'school_subject' => $this->getRelationValue('school_subject'),
			'school_subject_detailed' => $this->getRelationValue('school_subject_detailed'),
			'biblioteca' => $this->getRelationValue('biblioteca'),
		];
	}
}
