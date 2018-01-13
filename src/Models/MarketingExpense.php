<?php

namespace Inspirium\BookProposition\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Inspirium\BookProposition\Models\MarketingExpense
 *
 * @property int $id
 * @property int $proposition_id
 * @property string|null $marketing_expense
 * @property string|null $additional_expense
 * @property string|null $type
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read mixed $total
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\MarketingExpense whereAdditionalExpense($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\MarketingExpense whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\MarketingExpense whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\MarketingExpense whereMarketingExpense($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\MarketingExpense wherePropositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\MarketingExpense whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\MarketingExpense whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $expense
 * @property-read \Illuminate\Database\Eloquent\Collection|\Inspirium\BookProposition\Models\AdditionalExpense[] $additionalExpenses
 * @property-read mixed $totals
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\MarketingExpense whereExpense($value)
 * @property int|null $parent_id
 * @property-read \Inspirium\BookProposition\Models\MarketingExpense|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\MarketingExpense whereParentId($value)
 */
class MarketingExpense extends Model implements Auditable {

	use \OwenIt\Auditing\Auditable;

	protected $table = 'marketing_expenses';

	protected $fillable = ['type', 'proposition_id', 'parent_id'];

	protected $appends = ['totals', 'additional_expense'];

	protected $with = ['additionalExpenses', 'parent'];

	public function getTotalsAttribute() {
		return $this->expense + $this->additionalExpenses->sum('amount');
	}

	public function additionalExpenses() {
		return $this->morphMany('Inspirium\BookProposition\Models\AdditionalExpense', 'connection');
	}

	public function parent() {
		return $this->belongsTo(MarketingExpense::class, 'parent_id');
	}

	public function getAdditionalExpenseAttribute() {
		//$out = $this->additionalExpenses;
		if ($this->type === 'expense') {
			foreach($this->parent->additionalExpenses as $a) {
				$a->load('child');
				if (!$a->child && !$a->parent) {
					$e = AdditionalExpense::create(['expense' => $a->expense, 'connection_id' => $this->id, 'connection_type' => $a->connection_type, 'parent_id' => $a->id]);
					$e->parent = $a;
					$this->additionalExpenses->push($e);
				}
			}
		}
	}
}