<?php

namespace Inspirium\BookProposition\Models;

use Carbon\Carbon;
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
	    'marketing_additional_expense' => 'array'
    ];

    public function owner() {
    	return $this->belongsTo('Inspirium\UserManagement\Models\User', 'owner_id');
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
    	return $this->hasMany('Inspirium\BookProposition\Models\PropositionNote');
	}

	public function getNotesAttribute(){
		return $this->attributes['notes'] = $this->getRelationValue('notes')->keyBy('type');
	}

	public function getAuthorsAttribute(){
		return $this->attributes['authors'] = $this->getRelationValue('authors')->keyBy('id');
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
		if (!$value) {
			return [];
		}
		return json_decode($value, true);
	}

	public function getOffersAttribute() {
		return $this->attributes['offers'] = $this->getRelationValue('options')->keyBy('id');
	}

	public function getDeadlineAttribute($value) {
		return date('d. m. Y.', strtotime($value));
	}

	public function setDeadlineAttribute($value) {
		if (!$value) {
			$this->attributes['deadline'] = null;
		}
		else {
			$date = Carbon::createFromFormat( 'd. m. Y.', $value );
			$date->setTime( 0, 0, 0, 0 );
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
}
