<?php

namespace Inspirium\BookProposition\Controllers;

use App\Http\Controllers\Controller;
use Inspirium\BookManagement\Models\BookCategory;
use Inspirium\BookProposition\Models\BookProposition;

class PropositionController extends Controller {

    public function show() {
        return view(config('app.template') . '::proposition.list');
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
