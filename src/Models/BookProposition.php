<?php

namespace Inspirium\BookProposition\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Inspirium\BookManagement\Models\Book;
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
	    'school_level' => 'array',
	    'possible_products' => 'array'
    ];

    //relationships
	//one-to-many
    public function owner() {
    	return $this->belongsTo('Inspirium\HumanResources\Models\Employee', 'owner_id');
    }

	public function notes() {
		return $this->hasMany('Inspirium\BookProposition\Models\PropositionNote', 'proposition_id');
	}

	public function options() {
		return $this->hasMany( 'Inspirium\BookProposition\Models\PropositionOption', 'proposition_id' );
	}

	//many-to-many
	public function authorExpenses() {
		return $this->hasMany('Inspirium\BookProposition\Models\AuthorExpense', 'proposition_id');
	}

	public function getAuthorExpensesAttribute(){
		return $this->attributes['author_expenses'] = $this->getRelationValue('authorExpenses')->keyBy('author_id');
	}

	//polymorph
	public function authors() {
        return $this->morphToMany('Inspirium\BookManagement\Models\Author', 'connection', 'author_pivot', 'connection_id', 'author_id');
    }

	public function bookCategories() {
		return $this->morphToMany('Inspirium\BookManagement\Models\BookCategory', 'connection', 'book_category_pivot', 'connection_id', 'book_category_id');
	}

	public function bibliotecas() {
		return $this->morphToMany('Inspirium\BookManagement\Models\BookBiblioteca', 'connection', 'biblioteca_pivot', 'connection_id', 'biblioteca_id');
	}

	public function bookTypes() {
		return $this->morphToMany('Inspirium\BookManagement\Models\BookType', 'connection', 'book_type_pivot', 'connection_id', 'book_type_id');
	}

	public function schoolSubjects() {
		return $this->morphToMany('Inspirium\BookManagement\Models\SchoolSubject', 'connection', 'school_subjects_pivot', 'connection_id', 'school_subject_id');
	}

	public function schoolTypes() {
		return $this->morphToMany('Inspirium\BookManagement\Models\SchoolType', 'connection', 'school_type_pivot', 'connection_id', 'school_type_id');
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
		if (is_array($value)) {
			return $value;
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

	public function getCirculationsAttribute() {
		$out = [];
		foreach ($this->getRelationValue('options') as $option) {
			$out[] = [
				'title' => $option->title,
				'id' => $option->id
			];
		}
		return $out;
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
			'supergroup' => $this->bookCategories->parent->parent,
			'upgroup' => $this->bookCategories->parent,
			'group' => $this->bookCategories,
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
