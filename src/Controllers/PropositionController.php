<?php

namespace Inspirium\BookProposition\Controllers;

use App\Http\Controllers\Controller;
use Inspirium\BookManagement\Models\BookCategory;

class PropositionController extends Controller {

    public function show() {
        return view(config('app.template') . '::proposition.list');
    }

    public function edit( $id = null ) {
        //get step, return view
        return view(config('app.template') . '::proposition.edit');
    }

    public function categorization() {
        $supergroups = BookCategory::where('parent', 0)->get();
        return view(config('app.template') . '::proposition.categorization', compact('supergroups'));
    }
}
