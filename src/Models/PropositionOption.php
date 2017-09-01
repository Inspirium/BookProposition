<?php

namespace Inspirium\BookProposition\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * Class PropositionOption
 * @package Inspirium\BookProposition\Models
 *
 * @property $id
 * @property $title
 * @property $print_offer
 * @property $cover_type
 * @property $colors
 * @property $colors_first_page
 * @property $color_last_page
 * @property $additional_work
 * @property $cover_paper_type
 * @property $cover_colors
 * @property $cover_plastification
 * @property $film_print
 * @property $blind_print
 * @property $uv_print
 * @property $number_of_pages
 * @property $direct_cost_cover
 * @property $complete_cost_cover
 */
class PropositionOption extends Model {
	protected $table = 'proposition_options';

	protected $guarded = [];

	protected $appends = [
		'total_cost',
                            'complete_expense',
                            'remainder_after_sales',
	];

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

	public function getTotalCostAttribute() { return 0; }

	public function getCompleteExpenseAttribute() { return 0; }
	public function getRemainderAfterSalesAttribute() { return 0; }

}