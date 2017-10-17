<?php
namespace Inspirium\BookProposition\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Inspirium\BookProposition\Models\ApprovalRequest
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $budget
 * @property string $expense
 * @property int $proposition_id
 * @property int $requester_id
 * @property int $requestee_id
 * @property int $connection_id
 * @property string $connection_type
 * @property \Carbon\Carbon|null $deleted_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $connection
 * @property-read \Inspirium\BookProposition\Models\BookProposition $proposition
 * @property-read \Inspirium\HumanResources\Models\Employee $requestee
 * @property-read \Inspirium\HumanResources\Models\Employee $requester
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\Inspirium\BookProposition\Models\ApprovalRequest onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ApprovalRequest whereBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ApprovalRequest whereConnectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ApprovalRequest whereConnectionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ApprovalRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ApprovalRequest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ApprovalRequest whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ApprovalRequest whereExpense($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ApprovalRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ApprovalRequest whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ApprovalRequest wherePropositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ApprovalRequest whereRequesteeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ApprovalRequest whereRequesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\ApprovalRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Inspirium\BookProposition\Models\ApprovalRequest withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\Inspirium\BookProposition\Models\ApprovalRequest withoutTrashed()
 * @mixin \Eloquent
 */
class ApprovalRequest extends Model {

	use SoftDeletes;

	protected $table = 'approval_requests';

	protected $dates = ['deleted_at'];

	protected $observables = ['assigned'];

	protected $fillable = ['name', 'description', 'budget', 'expense', 'requester_id', 'requestee_id', 'proposition_id', 'status'];

	public function proposition() {
		return $this->belongsTo('Inspirium\BookProposition\Models\BookProposition', 'proposition_id');
	}

	public function requester() {
		return $this->belongsTo('Inspirium\HumanResources\Models\Employee', 'requester_id');
	}

	public function requestees() {
		return $this->belongsToMany('Inspirium\HumanResources\Models\Employee', 'approval_requests_requestees_pivot', 'approval_request_id', 'requestee_id');
	}

	//todo: not happy with this
	public function triggerAssigned() {
		$this->fireModelEvent('assigned', false);
	}
}