<?php
namespace Inspirium\BookProposition\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Inspirium\BookProposition\Models\AdditionalExpense
 *
 * @property int $id
 * @property string|null $expense
 * @property string|null $amount
 * @property string|null $type
 * @property int|null $connection_id
 * @property string|null $connection_type
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $connection
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\AdditionalExpense whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\AdditionalExpense whereConnectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\AdditionalExpense whereConnectionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\AdditionalExpense whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\AdditionalExpense whereExpense($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\AdditionalExpense whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\AdditionalExpense whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\AdditionalExpense whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AdditionalExpense extends Model {
	protected $table = 'additional_expenses';

	public function connection() {
		return $this->morphTo();
	}
}