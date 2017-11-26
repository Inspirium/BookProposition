<?php

namespace Inspirium\BookProposition\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Inspirium\Models\BookManagement\Book;
use Inspirium\FileManagement\Models\File;

/**
 * Inspirium\BookProposition\Models\BookProposition
 *
 * @property int $id
 * @property int|null $owner_id
 * @property string|null $project_name
 * @property string|null $project_number
 * @property string|null $additional_project_number
 * @property string|null $title
 * @property string|null $status
 * @property string|null $concept
 * @property string|null $manuscript
 * @property int|null $dotation
 * @property string|null $dotation_origin
 * @property string|null $dotation_amount
 * @property array $possible_products
 * @property array $school_level
 * @property int|null $school_assignment
 * @property string|null $main_target
 * @property array $additions
 * @property string|null $circulations
 * @property string|null $number_of_pages
 * @property string|null $width
 * @property string|null $height
 * @property string|null $paper_type
 * @property string|null $additional_work
 * @property string|null $colors
 * @property string|null $colors_first_page
 * @property string|null $colors_last_page
 * @property string|null $cover_type
 * @property string|null $cover_paper_type
 * @property string|null $cover_colors
 * @property string|null $cover_plastification
 * @property int|null $film_print
 * @property int|null $blind_print
 * @property int|null $uv_print
 * @property string|null $book_binding
 * @property array $author_other_expense
 * @property string|null $text_price
 * @property string|null $text_price_amount
 * @property string|null $accontation
 * @property string|null $netto_price_percentage
 * @property string|null $reviews
 * @property string|null $lecture
 * @property string|null $lecture_amount
 * @property string|null $correction
 * @property string|null $correction_amount
 * @property string|null $proofreading
 * @property string|null $proofreading_amount
 * @property string|null $translation
 * @property string|null $translation_amount
 * @property string|null $index
 * @property string|null $index_amount
 * @property string|null $epilogue
 * @property string|null $photos
 * @property string|null $photos_amount
 * @property string|null $illustrations
 * @property string|null $illustrations_amount
 * @property string|null $technical_drawings
 * @property string|null $technical_drawings_amount
 * @property string|null $expert_report
 * @property string|null $copyright
 * @property string|null $copyright_mediator
 * @property string|null $selection
 * @property string|null $powerpoint_presentation
 * @property string|null $methodical_instrumentarium
 * @property array $production_additional_expense
 * @property string|null $marketing_expense
 * @property array $marketing_additional_expense
 * @property string|null $margin
 * @property string|null $layout_complexity
 * @property int|null $layout_include
 * @property string|null $design_complexity
 * @property int|null $design_include
 * @property string|null $layout_note
 * @property string|null $design_note
 * @property \Carbon\Carbon|null $deadline
 * @property string|null $priority
 * @property array $expenses
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Inspirium\BookProposition\Models\AuthorExpense[] $authorExpenses
 * @property-read \Illuminate\Database\Eloquent\Collection|\Inspirium\Models\BookManagement\Author[] $authors
 * @property-read \Illuminate\Database\Eloquent\Collection|\Inspirium\Models\BookManagement\BookBiblioteca[] $bibliotecas
 * @property-read \Illuminate\Database\Eloquent\Collection|\Inspirium\Models\BookManagement\BookCategory[] $bookCategories
 * @property-read \Illuminate\Database\Eloquent\Collection|\Inspirium\Models\BookManagement\BookType[] $bookTypes
 * @property-read \Illuminate\Database\Eloquent\Collection|\Inspirium\FileManagement\Models\File[] $documents
 * @property-read mixed $offers
 * @property-read mixed $school_type
 * @property-read \Illuminate\Database\Eloquent\Collection|\Inspirium\BookProposition\Models\PropositionNote[] $notes
 * @property-read \Illuminate\Database\Eloquent\Collection|\Inspirium\BookProposition\Models\PropositionOption[] $options
 * @property-read \Inspirium\HumanResources\Models\Employee|null $owner
 * @property-read \Illuminate\Database\Eloquent\Collection|\Inspirium\Models\BookManagement\SchoolSubject[] $schoolSubjects
 * @property-read \Illuminate\Database\Eloquent\Collection|\Inspirium\Models\BookManagement\SchoolType[] $schoolTypes
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\Inspirium\BookProposition\Models\BookProposition onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereAccontation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereAdditionalProjectNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereAdditionalWork($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereAdditions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereAuthorOtherExpense($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereBlindPrint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereBookBinding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereCirculations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereColors($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereColorsFirstPage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereColorsLastPage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereConcept($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereCopyright($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereCopyrightMediator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereCorrection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereCorrectionAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereCoverColors($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereCoverPaperType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereCoverPlastification($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereCoverType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereDeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereDesignComplexity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereDesignInclude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereDesignNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereDotation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereDotationAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereDotationOrigin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereEpilogue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereExpenses($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereExpertReport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereFilmPrint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereIllustrations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereIllustrationsAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereIndexAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereLayoutComplexity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereLayoutInclude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereLayoutNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereLecture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereLectureAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereMainTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereManuscript($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereMargin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereMarketingAdditionalExpense($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereMarketingExpense($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereMethodicalInstrumentarium($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereNettoPricePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereNumberOfPages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition wherePaperType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition wherePhotos($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition wherePhotosAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition wherePossibleProducts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition wherePowerpointPresentation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereProductionAdditionalExpense($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereProjectName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereProjectNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereProofreading($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereProofreadingAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereReviews($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereSchoolAssignment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereSchoolLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereSelection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereTechnicalDrawings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereTechnicalDrawingsAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereTextPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereTextPriceAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereTranslation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereTranslationAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereUvPrint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\BookProposition whereWidth($value)
 * @method static \Illuminate\Database\Query\Builder|\Inspirium\BookProposition\Models\BookProposition withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\Inspirium\BookProposition\Models\BookProposition withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\Inspirium\BookProposition\Models\ProductionExpense[] $productionExpenses
 * @property-read \Illuminate\Database\Eloquent\Collection|\Inspirium\BookProposition\Models\MarketingExpense[] $marketingExpenses
 * @property-read \Illuminate\Database\Eloquent\Collection|\Inspirium\BookProposition\Models\AdditionalExpense[] $authorOtherExpenses
 * @property-read \Illuminate\Database\Eloquent\Collection|\Inspirium\BookProposition\Models\ApprovalRequest[] $approvalRequests
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
    	'additions' => 'array',
	    'school_level' => 'array',
	    'possible_products' => 'array',
	    'marketing_additional_expense' => 'array',
	    'author_other_expense' => 'array',
	    'expenses' => 'array',
	    'price_first_year' => 'array',
	    'price_second_year' => 'array'
    ];

    //relationships
	//one-to-many
    public function owner() {
    	return $this->belongsTo('Inspirium\Models\HumanResources\Employee', 'owner_id');
    }

	public function notes() {
		return $this->hasMany('Inspirium\BookProposition\Models\PropositionNote', 'proposition_id');
	}

	public function options() {
		return $this->hasMany( 'Inspirium\BookProposition\Models\PropositionOption', 'proposition_id' );
	}

	public function authorExpenses() {
		return $this->hasMany('Inspirium\BookProposition\Models\AuthorExpense', 'proposition_id');
	}

	public function productionExpenses() {
    	return $this->hasMany('Inspirium\BookProposition\Models\ProductionExpense', 'proposition_id');
	}

	public function marketingExpenses() {
		return $this->hasMany('Inspirium\BookProposition\Models\MarketingExpense', 'proposition_id');
	}

	public function approvalRequests() {
    	return $this->hasMany('Inspirium\BookProposition\Models\ApprovalRequest', 'proposition_id');
	}

	//polymorph
	public function authors() {
        return $this->morphToMany('Inspirium\Models\BookManagement\Author', 'connection', 'author_pivot', 'connection_id', 'author_id');
    }

	public function bookCategories() {
		return $this->morphToMany('Inspirium\Models\BookManagement\BookCategory', 'connection', 'book_category_pivot', 'connection_id', 'book_category_id');
	}

	public function bibliotecas() {
		return $this->morphToMany('Inspirium\Models\BookManagement\BookBiblioteca', 'connection', 'biblioteca_pivot', 'connection_id', 'biblioteca_id');
	}

	public function bookTypes() {
		return $this->morphToMany('Inspirium\Models\BookManagement\BookType', 'connection', 'book_type_pivot', 'connection_id', 'book_type_id');
	}

	public function schoolSubjects() {
		return $this->morphToMany('Inspirium\Models\BookManagement\SchoolSubject', 'connection', 'school_subjects_pivot', 'connection_id', 'school_subject_id');
	}

	public function schoolTypes() {
		return $this->morphToMany('Inspirium\Models\BookManagement\SchoolType', 'connection', 'school_type_pivot', 'connection_id', 'school_type_id');
	}

	public function documents() {
		return $this->morphToMany('Inspirium\FileManagement\Models\File', 'fileable')->withPivot('type');
	}

	public function authorOtherExpenses() {
    	return $this->morphMany('Inspirium\BookProposition\Models\AdditionalExpense', 'connection');
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

	public function getExpensesAttribute($value){
		if ($value) {
			if (is_array($value)) {
				return $value;
			}
			else {
				return json_decode($value, true);
			}
		}
		$authors = $this->authors()->get(['id'])->mapWithKeys(function($author) {
			return [$author->id => 0];
		});
		return [
			'authors' => $authors,
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
			'technical_drawings' => 0
		];
	}

	public function getPriceFirstYearAttribute($value) {
    	if (!$value) {
    		return [
			    'retail' => 0,
			    'wholesale' => 0,
			    'direct' => 0,
			    'field' => 0,
			    'promotors' => 0,
			    'export' => 0
		    ];

	    }
	    return json_decode($value, true);
	}

	public function getPriceSecondYearAttribute($value) {
		if (!$value) {
			return [
				'retail' => 0,
				'wholesale' => 0,
				'direct' => 0,
				'field' => 0,
				'promotors' => 0,
				'export' => 0
			];

		}
		return json_decode($value, true);
	}
}
