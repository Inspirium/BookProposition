<?php

namespace Inspirium\BookProposition\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BookProposition
 * @package Inspirium\BookProposition\Models
 *
 * @property $id
 * @property $owner_id
 */
class BookProposition extends Model {
    use SoftDeletes;

    protected $table = 'propositions';

    protected $fillable = [
        'id', 'title',  'concept', 'manuscript', 'dotation', 'dotation_origin', 'dotaion_amount',
        'possible_products', 'supergroup_id', 'upgroup_id',
	    'group_id', 'book_type_group_id', 'book_type_id',
	    'school_type', 'school_level', 'school_assignment',
	    'school_subject_id', 'school_subject_detailed_id',
	    'biblioteca', 'main_target', 'status'
    ];

    protected $casts = [
        'title' => 'string',
        'concept' => 'string',
        'manuscript' => 'string',
        'dotation' => 'boolean',
        'dotation_origin' => 'string',
        'dotation_amount' => 'float',
        'possible_products' => 'array',
	    'supergroup_id' => 'integer',
	    'upgroup_id' => 'integer',
	    'group_id' => 'integer',
	    'book_type_group_id' => 'integer',
	    'book_type_id' => 'integer',
	    'school_type' => 'string',
	    'school_level' => 'array',
	    'school_assignment' => 'boolean',
	    'school_subject_id' => 'integer',
	    'school_subject_detailed_id' => 'integer',
	    'biblioteca' => 'integer',
	    'main_target' => 'string',
	    'status' => 'string'
    ];

    public function owner() {
    	return $this->belongsTo('Inspirium\UserManagement\Models\User', 'owner_id');
    }

    public function authorExpenses() {
        return $this->hasMany('Inspirium\BookProposition\Models\AuthorExpense', 'proposition_id');
    }

    public function authors() {
        return $this->belongsToMany('Inspirium\BookManagement\Models\Author', 'pivot_proposition_author', 'author_id', 'proposition_id');
    }

	public function notes() {
    	return $this->hasMany('Inspirium\BookProposition\Models\PropositionNote');
	}

	public function getNotesAttribute(){
		return $this->attributes['notes'] = $this->getRelationValue('notes')->keyBy('type');
	}
}
