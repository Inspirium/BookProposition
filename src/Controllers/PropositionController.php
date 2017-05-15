<?php

namespace Inspirium\BookProposition\Controllers;

use App\Http\Controllers\Controller;

class PropositionController extends Controller {

    public function show() {
        return view(config('app.template') . '::proposition.list');
    }

    public function edit( $id = null ) {
        //get step, return view
        return view(config('app.template') . '::proposition.basic_data');
    }
}
