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
 * @property-read \Inspirium\BookManagement\Models\Author|null $author
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
 */
class AuthorExpense extends Model {
    protected $table = 'author_expenses';

    protected $guarded = [];

    protected $casts = [
        'additional_expenses' => 'array',
    ];

    protected $fillable = ['author_id', 'amount', 'percentage', 'accontation', 'additional_expenses'];

    protected $appends = ['total'];

    public function author() {
        return $this->belongsTo('Inspirium\BookManagement\Models\Author');
    }

    public function proposition() {
    	return $this->belongsTo('Inspirium\BookProposition\Models\BookProposition', 'proposition_id');
    }

    public function getAdditionalExpensesAttribute($value) {
    	if (!$value) {
    		return [];
	    }
	    else if (is_array($value)) {
    		return $value;
	    }
    	return json_decode($value, true);
    }

    public function getTotalAttribute() {
    	return $this->attributes['amount'] + collect($this->attributes['additional_expenses'])->sum('amount');
    }
}
