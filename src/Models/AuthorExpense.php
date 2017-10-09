<?php

namespace Inspirium\BookProposition\Models;

use Illuminate\Database\Eloquent\Model;

class AuthorExpense extends Model {
    protected $table = 'author_expenses';

    protected $guarded = [];

    protected $casts = [
        'additional_expenses' => 'array',
	    'amount' => 'float',
	    'percentage' => 'float',
	    'accontation' => 'float',
    ];

    protected $fillable = ['author_id', 'amount', 'percentage', 'accontation'];

    public function author() {
        return $this->belongsTo('Inspirium\BookManagement\Models\Author');
    }

    public function proposition() {
    	return $this->belongsTo('Inspirium\BookProposition\Models\BookProposition', 'proposition_id');
    }
}
