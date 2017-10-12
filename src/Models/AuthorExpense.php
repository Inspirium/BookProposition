<?php

namespace Inspirium\BookProposition\Models;

use Illuminate\Database\Eloquent\Model;

class AuthorExpense extends Model {
    protected $table = 'author_expenses';

    protected $guarded = [];

    protected $casts = [
        'additional_expenses' => 'array',
    ];

    protected $fillable = ['author_id', 'amount', 'percentage', 'accontation', 'additional_expenses'];

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
}
