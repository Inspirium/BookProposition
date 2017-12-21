<?php
namespace Inspirium\BookProposition\Controllers\Api;

use Inspirium\BookProposition\Models\BookProposition;
use Inspirium\Http\Controllers\Controller;

class PropositionsController extends Controller {

	public function approval() {
		$propositions = BookProposition::where('status', 'requested')->with('owner')->get();
		$total = \DB::table('propositions')->where('status', 'requested')->count();
		return response()->json(['rows' => $propositions, 'total' => $total]);
	}

	public function unfinished() {
		$propositions = BookProposition::where('status', 'unfinished')->with('owner')->get();
		$total = \DB::table('propositions')->where('status', 'unfinished')->count();
		return response()->json(['rows' => $propositions, 'total' => $total]);
	}

	public function active() {
		$propositions = BookProposition::where('status', 'approved')->with('owner')->get();
		$total = \DB::table('propositions')->where('status', 'approved')->count();
		return response()->json(['rows' => $propositions, 'total' => $total]);
	}

	public function rejected() {
		$propositions = BookProposition::where('status', 'rejected')->with('owner')->get();
		$total = \DB::table('propositions')->where('status', 'rejected')->count();
		return response()->json(['rows' => $propositions, 'total' => $total]);
	}

	public function deleted() {
		$propositions = BookProposition::onlyTrashed()->with('owner')->get();
		$total = \DB::table('propositions')->whereNotNull('deleted_at')->count();
		return response()->json(['rows' => $propositions, 'total' => $total]);
	}

	public function archive() {
		$propositions = BookProposition::where('status', 'archived')->with('owner')->get();
		$total = \DB::table('propositions')->where('status', 'archived')->count();
		return response()->json(['rows' => $propositions, 'total' => $total]);
	}
}