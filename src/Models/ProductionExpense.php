<?php

namespace Inspirium\BookProposition\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Inspirium\BookProposition\Models\ProductionExpense
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int $proposition_id
 * @property string|null $type
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
 * @property string|null $additional_expense
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read mixed $totals
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereAccontation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereAdditionalExpense($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereCopyright($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereCopyrightMediator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereCorrection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereCorrectionAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereEpilogue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereExpertReport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereIllustrations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereIllustrationsAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereIndexAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereLecture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereLectureAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereMethodicalInstrumentarium($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereNettoPricePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense wherePhotos($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense wherePhotosAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense wherePowerpointPresentation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereProofreading($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereProofreadingAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense wherePropositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereReviews($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereSelection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereTechnicalDrawings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereTechnicalDrawingsAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereTextPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereTextPriceAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereTranslation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereTranslationAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\Inspirium\BookProposition\Models\AdditionalExpense[] $additionalExpenses
 * @property int|null $parent_id
 * @property-read \Inspirium\BookProposition\Models\ProductionExpense|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereParentId($value)
 * @property string|null $layout_complexity
 * @property int|null $layout_include
 * @property string|null $design_complexity
 * @property int|null $design_include
 * @property-read \Inspirium\BookProposition\Models\BookProposition $proposition
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereDesignComplexity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereDesignInclude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereLayoutComplexity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereLayoutInclude($value)
 * @property string|null $distribution_margin
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ProductionExpense whereDistributionMargin($value)
 */
class ProductionExpense extends Model implements Auditable {

	use \OwenIt\Auditing\Auditable;

	protected $table = 'production_expenses';

	protected $fillable = ['type', 'parent_id', 'proposition_id'];

	protected $appends = ['totals', 'additional_expense'];

	protected $with = ['additionalExpenses', 'parent'];

	public function getTotalsAttribute() {
		$out =  [
			'text_price' => $this->text_price * $this->text_price_amount,
			'reviews' => $this->reviews,
			'lecture' => $this->lecture * $this->lecture_amount,
			'correction' => $this->correction * $this->correction_amount,
			'proofreading' => $this->proofreading * $this->proofreading_amount,
			'translation' => $this->translation * $this->translation_amount,
			'index' => $this->index * $this->index_amount,
			'epilogue' => $this->epilogue,
			'photos' => $this->photos * $this->photos_amount,
			'illustrations' => $this->illustrations * $this->illustrations_amount,
			'technical_drawings' => $this->technical_drawings * $this->technical_drawings_amount,
			'expert_report' => $this->expert_report,
			'copyright' => $this->copyright,
			'copyright_mediator' => $this->copyright_mediator,
			'methodical_instrumentarium' => $this->methodical_instrumentarium,
			'selection' => $this->selection,
			'powerpoint_presentation' => $this->powerpoint_presentation,
			'additional_expenses' => $this->additionalExpenses->sum('amount')
		];
		$out['total'] = collect($out)->sum();
		$out['layout'] = $this->calcDesignLayoutExpense();
		$out['accontation'] = $this->accontation;
		return $out;
	}

	private function calcDesignLayoutExpense() {
		$lc = 3;
		$dc = 3;
		if ($this->layout_complexity) {
			$lc = $this->layout_complexity;
		}
		if ($this->design_complexity) {
			$dc = $this->design_complexity;
		}
		$lcomplexity = [
			1 => 0.65,
			2 => 0.8,
			3 => 1,
			4 => 1.2,
			5 => 1.35
		];
		$rcomplexity = [
			1 => 0.4,
			2 => 0.7,
			3 => 1,
			4 => 1.3,
			5 => 1.6
		];
		$this->load('proposition');
		$group = $this->proposition->bookCategories()->with('parent')->first();
		if ($group) {
			$coefficient = $group->parent->coefficient / 60;
		}
		else {
			$coefficient = 1;
		}
		$number_of_hours = ($coefficient * $this->proposition->number_of_pages + $this->photos_amount/30 + $this->illustrations_amount/30 + $this->technical_drawings_amount/30) * $lcomplexity[$lc];

		if ($this->layout_exact_price) {
			$layout = $this->layout_exact_price;
		}
		else {
			$layout = $number_of_hours * 8000/175;
		}

		if ($this->design_exact_price) {
			$design = $this->design_exact_price;
		}
		else {
			$design = $number_of_hours * 15000 / 175 * $rcomplexity[$dc]/2;
		}
		return $layout + $design;
	}

	public function proposition() {
		return $this->belongsTo('Inspirium\BookProposition\Models\BookProposition', 'proposition_id');
	}

	public function additionalExpenses() {
		return $this->morphMany('Inspirium\BookProposition\Models\AdditionalExpense', 'connection');
	}

	public function parent() {
		return $this->belongsTo(ProductionExpense::class, 'parent_id');
	}

	public function getAdditionalExpenseAttribute() {
		if ($this->type === 'expense') {
			foreach($this->parent->additionalExpenses as $a) {
				$a->load('child');
				if (!$a->child && !$a->parent) {
					$e = AdditionalExpense::create(['expense' => $a->expense, 'connection_id' => $this->id, 'connection_type' => $a->connection_type, 'parent_id' => $a->id]);
					$e->parent()->associate($a);
					$this->additionalExpenses->push($e);
				}
			}
		}
	}

}