<?php

namespace Inspirium\BookProposition\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Inspirium\BookProposition\Models\AuthorExpense
 *
 * @property int $id
 * @property int|null $proposition_id
 * @property int|null $author_id
 * @property string|null $amount
 * @property string|null $percentage
 * @property string|null $accontation
 * @property array $additional_expenses
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Inspirium\Models\BookManagement\Author|null $author
 * @property-read mixed $total
 * @property-read \Inspirium\BookProposition\Models\BookProposition|null $proposition
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\AuthorExpense whereAccontation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\AuthorExpense whereAdditionalExpenses($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\AuthorExpense whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\AuthorExpense whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\AuthorExpense whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\AuthorExpense whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\AuthorExpense wherePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\AuthorExpense wherePropositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\AuthorExpense whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $type
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\AuthorExpense whereType($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\Inspirium\BookProposition\Models\AdditionalExpense[] $additionalExpenses
 * @property-read mixed $totals
 * @property int|null $parent_id
 * @property-read mixed $additional_expense
 * @property-read \Inspirium\BookProposition\Models\AuthorExpense|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\AuthorExpense whereParentId($value)
 */
class AuthorExpense extends Model {
    protected $table = 'author_expenses';

    protected $guarded = [];

    protected $fillable = ['author_id', 'amount', 'percentage', 'accontation', 'type', 'parent_id'];

    protected $appends = ['totals', 'additional_expense'];

    protected $with = ['additionalExpenses', 'parent'];

    public function author() {
        return $this->belongsTo('Inspirium\Models\BookManagement\Author');
    }

    public function proposition() {
    	return $this->belongsTo('Inspirium\BookProposition\Models\BookProposition', 'proposition_id');
    }

    public function getTotalsAttribute() {
    	return $this->amount + $this->additionalExpenses->sum('amount');
    }

    public function additionalExpenses() {
    	return $this->morphMany('Inspirium\BookProposition\Models\AdditionalExpense', 'connection');
    }

    public function parent() {
    	return $this->belongsTo(AuthorExpense::class, 'parent_id');
    }

	public function getAdditionalExpenseAttribute() {
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
