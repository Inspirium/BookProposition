<?php

namespace Inspirium\BookProposition\Models;

use Illuminate\Database\Eloquent\Model;

class AuthorExpense extends Model {
    protected $table = 'author_expenses';

    public function author() {
        return $this->belongsTo('Inspirium\BookManagement\Models\Author');
    }
}
