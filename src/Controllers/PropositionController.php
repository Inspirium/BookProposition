<?php

namespace Inspirium\BookProposition\Controllers;

use App\Http\Controllers\Controller;
use Inspirium\BookManagement\Models\BookCategory;
use Inspirium\BookProposition\Models\BookProposition;

class PropositionController extends Controller {

    public function show() {
    	$approval = BookProposition::where('status', 'pending')->get();
    	$unfinished = BookProposition::where('status', 'unfinished')->get();
    	$active = BookProposition::where('status', 'active')->get();
    	$rejected = BookProposition::where('status', 'rejected')->get();
    	$deleted = BookProposition::onlyTrashed()->get();
        return view(config('app.template') . '::proposition.list', compact('approval', 'unfinished', 'active', 'rejected', 'deleted'));
    }

    public function edit( $id = null ) {
        $proposition = BookProposition::firstOrNew(['id' => $id]);
        return view(config('app.template') . '::proposition.edit', compact('proposition'));
    }

    public function categorization() {
        $supergroups = BookCategory::where('parent', 0)->get();
        return view(config('app.template') . '::proposition.categorization', compact('supergroups'));
    }
}
