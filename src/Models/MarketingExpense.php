<?php

namespace Inspirium\BookProposition\Models;

use Illuminate\Database\Eloquent\Model;

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
 */
class MarketingExpense extends Model {
	protected $table = 'marketing_expenses';

	protected $fillable = ['type', 'proposition_id'];

	protected $appends = ['totals'];

	public function getTotalsAttribute() {
		return [
			'expense' => $this->expense
		];
	}

	public function additionalExpenses() {
		return $this->morphMany('Inspirium\BookProposition\Models\AdditionalExpense', 'connection');
	}

}