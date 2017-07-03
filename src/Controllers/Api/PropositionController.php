<?php

namespace Inspirium\BookProposition\Controllers\Api;

use App\Http\Controllers\Controller;
use Inspirium\BookProposition\Models\BookProposition;

class PropositionController extends Controller {

	public function getProposition( $id ) {
		$proposition = BookProposition::findOrFail($id);
		//TODO: build proposition object according to access rights

		$out = [
			'basic_data' => [
				'title' => $proposition->title,
				'concept' => $proposition->concept,
				'possible_products' => $proposition->possible_products,
				'dotation' => $proposition->dotation,
				'dotation_amount' => $proposition->dotation_amount,
				'dotation_origin' => $proposition->dotation_origin,
				'manuscript' => $proposition->manuscript,
			]
		];
		return response()->json($out);
	}

	public function saveProposition( $id = null ) {
		$proposition = BookProposition::firstOrCreate(['id' => $id]);

	}
}