<?php

namespace Inspirium\BookProposition\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Inspirium\BookProposition\Models\PropositionNote
 *
 * @property int $id
 * @property string $type
 * @property int $proposition_id
 * @property string|null $note
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionNote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionNote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionNote whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionNote wherePropositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionNote whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Inspirium\BookProposition\Models\PropositionNote whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PropositionNote extends Model {
	protected $table = 'proposition_notes';

	protected $guarded = [];
}