<?php

namespace Inspirium\BookProposition\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inspirium\BookProposition\Models\BookProposition;

class PropositionController extends Controller {

	public function getProposition( $id ) {
		$proposition = BookProposition::findOrFail($id);
		$out = $this->buildResponse($proposition);
		return response()->json($out);
	}

	public function saveProposition( Request $request, $id = null ) {
		$proposition = BookProposition::firstOrCreate(['id' => $id]);
		switch ($request->input('step')) {
			case 'basic_data':
				$proposition->owner_id = Auth::id();
				$proposition->title = $request->input('data.title');
				$proposition->concept = $request->input('data.concept');
				break;
			case 'categorization':
				break;
		}
		$proposition->status = 'unfinished';
		$proposition->save();
		$out = $this->buildResponse($proposition);
		return response()->json($out);
	}

	private function buildResponse($proposition) {
		//TODO: build proposition object according to access rights
		$out = [
			'id' => $proposition->id,
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
		return $out;
	}
}