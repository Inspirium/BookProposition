<?php
namespace Inspirium\BookProposition\Controllers\Api;

use Illuminate\Http\Request;
use Inspirium\BookProposition\Models\BookProposition;
use Inspirium\Http\Controllers\Controller;

class PropositionsController extends Controller {

	public function approval(Request $request) {
		$limit = $request->input('limit');
		$offset = $request->input('offset');
		$order = $request->input('order');
		$sort = $request->input('sort');
		$propositions = BookProposition::where('status', 'requested')->orderBy($sort?$sort:'id', $order)->with('owner')->limit($limit)->offset($offset)->get();
		$total = \DB::table('propositions')->where('status', 'requested')->count();
		return response()->json(['rows' => $propositions, 'total' => $total]);
	}

	public function unfinished(Request $request) {
		$limit = $request->input('limit');
		$offset = $request->input('offset');
		$order = $request->input('order');
		$sort = $request->input('sort');
		$propositions = BookProposition::where('status', 'unfinished')->orderBy($sort?$sort:'id', $order)->with('owner')->limit($limit)->offset($offset)->get();
		$total = \DB::table('propositions')->where('status', 'unfinished')->count();
		return response()->json(['rows' => $propositions, 'total' => $total]);
	}

	public function active(Request $request) {
		$limit = $request->input('limit');
		$offset = $request->input('offset');
		$order = $request->input('order');
		$sort = $request->input('sort');
		$propositions = BookProposition::where('status', 'approved')->orderBy($sort?$sort:'id', $order)->with('owner')->limit($limit)->offset($offset)->get();
		$total = \DB::table('propositions')->where('status', 'approved')->count();
		return response()->json(['rows' => $propositions, 'total' => $total]);
	}

	public function rejected(Request $request) {
		$limit = $request->input('limit');
		$offset = $request->input('offset');
		$order = $request->input('order');
		$sort = $request->input('sort');
		$propositions = BookProposition::where('status', 'rejected')->orderBy($sort?$sort:'id', $order)->with('owner')->limit($limit)->offset($offset)->get();
		$total = \DB::table('propositions')->where('status', 'rejected')->count();
		return response()->json(['rows' => $propositions, 'total' => $total]);
	}

	public function deleted(Request $request) {
		$limit = $request->input('limit');
		$offset = $request->input('offset');
		$order = $request->input('order');
		$sort = $request->input('sort');
		$propositions = BookProposition::onlyTrashed()->orderBy($sort?$sort:'id', $order)->with('owner')->limit($limit)->offset($offset)->get();
		$total = \DB::table('propositions')->whereNotNull('deleted_at')->count();
		return response()->json(['rows' => $propositions, 'total' => $total]);
	}

	public function archive(Request $request) {
		$limit = $request->input('limit');
		$offset = $request->input('offset');
		$order = $request->input('order');
		$sort = $request->input('sort');
		$propositions = BookProposition::where('status', 'archived')->orderBy($sort?$sort:'id', $order)->with('owner')->limit($limit)->offset($offset)->get();
		$total = \DB::table('propositions')->where('status', 'archived')->count();
		return response()->json(['rows' => $propositions, 'total' => $total]);
	}
}