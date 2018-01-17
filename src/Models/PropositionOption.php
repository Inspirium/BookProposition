<?php

namespace Inspirium\BookProposition\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Inspirium\BookProposition\Models\PropositionOption
 *
 * @property int $id
 * @property int|null $proposition_id
 * @property string|null $title
 * @property string|null $print_offer
 * @property string|null $paper_type
 * @property string|null $cover_type
 * @property string|null $hard_cover_circulation
 * @property string|null $soft_cover_circulation
 * @property string|null $book_binding
 * @property string|null $colors
 * @property string|null $colors_first_page
 * @property string|null $colors_last_page
 * @property string|null $additional_work
 * @property string|null $cover_paper_type
 * @property string|null $cover_colors
 * @property string|null $cover_plastification
 * @property int|null $film_print
 * @property int|null $blind_print
 * @property int|null $uv_print
 * @property string|null $number_of_pages
 * @property string|null $compensation
 * @property string|null $indirect_expenses
 * @property string|null $price_proposal
 * @property string|null $calculated_profit_percent
 * @property string|null $shop_percent
 * @property string|null $vat_percent
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption whereAdditionalWork($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption whereBlindPrint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption whereBookBinding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption whereCalculatedProfitPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption whereColors($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption whereColorsFirstPage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption whereColorsLastPage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption whereCompensation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption whereCoverColors($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption whereCoverPaperType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption whereCoverPlastification($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption whereCoverType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption whereFilmPrint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption whereHardCoverCirculation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption whereIndirectExpenses($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption whereNumberOfPages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption wherePaperType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption wherePriceProposal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption wherePrintOffer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption wherePropositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption whereShopPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption whereSoftCoverCirculation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption whereUvPrint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption whereVatPercent($value)
 * @mixin \Eloquent
 * @property int $is_final
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionOption whereIsFinal($value)
 */
class PropositionOption extends Model implements Auditable {

	use \OwenIt\Auditing\Auditable;

	protected $table = 'proposition_options';

	protected $guarded = [];

	protected $with = ['files'];

	public function files() {
		return $this->morphToMany('Inspirium\Models\FileManagement\File', 'fileable')->withPivot('type');
	}

	//attributes
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

	public function mapModel($input) {
		$columns = Schema::getColumnListing($this->getTable());
		foreach ($columns as $i => $key) {
			if(isset($input[$key])) {
				$this->{$key} = $input[$key];
			}
		}
		return $this;
	}

	public function getFormattedStrings() {
		$book_bindings = [
			'hard' => __('Hard book binding'),
			'soft' => __('Soft book binding'),
			'spiral' => __('Spiral book binding'),
			'none' => __('None')
		];
		$colors = [
			'No data', 'One Colour', 'Two Colours', 'Three Colours', 'Full Colour', 'Fifth Colour'
		];
		$cover = [
			'none' => __('None'),
			'hard' => __('Hard Cover'),
			'soft' => __('Soft Cover'),
			'both' => __('Hard and Soft Cover')
		];
		$plastification = [
			'' => __('No data'),
			'none' => __('None'),
			'glossy' => __('Glossy plastification'),
			'mat' => __('Mat plastification')
		];
		return [
			'book_binding' => $this->book_binding?$book_bindings[$this->book_binding]:__('No data'),
			'colors' => $this->colors?$colors[$this->colors]:__('No data'),
			'colors_first_page' => $this->colors?$colors[$this->colors_first_page]:__('No data'),
			'colors_last_page' => $this->colors?$colors[$this->colors_last_page]:__('No data'),
			'cover_colors' => $this->colors?$colors[$this->cover_colors]:__('No data'),
			'cover_type' => $this->cover_type?$cover[$this->cover_type]:__('No data'),
			'plastification' => $this->cover_plastification?$plastification[$this->cover_plastification]:__('No data'),
			'film_print' => __( ucfirst($this->film_print) ),
			'blind_print' => __( ucfirst($this->film_print) ),
			'uv_print' => __( ucfirst($this->film_print) ),
		];
	}

}