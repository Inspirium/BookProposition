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
        'id', 'title', 'status', 'step', 'concept', 'manuscript', 'dotation', 'dotation_origin', 'dotaion_amount',
        'possible_products', 'basic_data_note',
        'created_at', 'update_at', 'deleted_at'
    ];
    protected $visible = [
        'id', 'title', 'status', 'step', 'concept', 'manuscript', 'dotation', 'dotation_origin', 'dotaion_amount',
        'possible_products', 'basic_data_note',
        'created_at', 'update_at', 'deleted_at'
    ];

    protected $casts = [
        'title' => 'string',
        'concept' => 'string',
        'manuscript' => 'string',
        'dotation' => 'boolean',
        'dotation_origin' => 'string',
        'dotation_amount' => 'float',
        'possible_products' => 'array',
        'basic_data_note' => 'string'
    ];

    public function authorExpenses() {
        return $this->hasMany('Inspirium\BookProposition\Models\AuthorExpense', 'proposition_id');
    }

    public function authors() {
        return $this->belongsToMany('Inspirium\BookManagement\Models\Author', 'pivot_proposition_author', 'author_id', 'proposition_id');
    }

}
