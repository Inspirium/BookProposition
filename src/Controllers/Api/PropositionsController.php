<?php
namespace Inspirium\BookProposition\Controllers\Api;

use Illuminate\Http\Request;
use Inspirium\BookProposition\Models\BookProposition;
use Inspirium\Http\Controllers\Controller;

class PropositionsController extends Controller {

    public function search(Request $request) {
        $term = $request->input('term');
        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 0);
        $propositions = BookProposition::where('title', 'like', "%$term%")
            ->with(['owner'])
            ->limit($limit)
            ->offset($offset)
            ->get();

        return response()->json(['rows' => $propositions]);
    }

	public function approval(Request $request) {
		$limit = $request->input('limit', 10);
		$offset = $request->input('offset', 0);
		$order = $request->input('order');
        if (!$order) {
            $order = 'asc';
        }
		$sort = $request->input('sort');
		$user = \Auth::user();
		if ($user->hasRole('access_all_propositions')) {
			$propositions = BookProposition::where('status', 'requested')
			                               ->orderBy($sort?$sort:'id', $order)
			                               ->with(['owner'])
			                               ->limit($limit)
			                               ->offset($offset)
			                               ->get();
			$total = BookProposition::where('status', 'requested')->count();
		}
		else {
			$propositions = BookProposition::where('status', 'requested')
			                               ->where('owner_id', $user->id)
			                               ->orderBy($sort?$sort:'id', $order)
			                               ->with(['owner'])
			                               ->limit($limit)
			                               ->offset($offset)
			                               ->get();
			$total = BookProposition::where('status', 'requested')->where('owner_id', $user->id)->count();
		}


		return response()->json(['rows' => $propositions, 'total' => $total]);
	}

	public function unfinished(Request $request) {
		$limit = $request->input('limit', 10);
		$offset = $request->input('offset', 0);
		$order = $request->input('order');
        if (!$order) {
            $order = 'asc';
        }
		$sort = $request->input('sort');
		$user = \Auth::user();
		if ($user->hasRole('access_all_propositions')) {
			$propositions = BookProposition::where( 'status', 'unfinished' )->orderBy( $sort ? $sort : 'id', $order )->with( 'owner' )->limit( $limit )->offset( $offset )->get();
			$total = BookProposition::where('status', 'unfinished')->count();
		}
		else {
			$propositions = BookProposition::where( 'status', 'unfinished' )->where( 'owner_id', $user->id )->orderBy( $sort ? $sort : 'id', $order )->with( 'owner' )->limit( $limit )->offset( $offset )->get();
			$total = BookProposition::where('status', 'unfinished')->where('owner_id', $user->id)->count();
		}

		return response()->json(['rows' => $propositions, 'total' => $total]);
	}

	public function active(Request $request) {
		$limit = $request->input('limit', 10);
		$offset = $request->input('offset', 0);
		$order = $request->input('order');
        if (!$order) {
            $order = 'asc';
        }
		$sort = $request->input('sort');
		$user = \Auth::user();
		if ($user->hasRole('access_all_propositions')) {
			$propositions = BookProposition::where( 'status', 'approved' )->orderBy( $sort ? $sort : 'id', $order )->with( 'owner' )->limit( $limit )->offset( $offset )->get();
			$total = BookProposition::where('status', 'approved')->count();
		}
		else {
			$propositions = BookProposition::where( 'status', 'approved' )->where( 'owner_id', $user->id )->orderBy( $sort ? $sort : 'id', $order )->with( 'owner' )->limit( $limit )->offset( $offset )->get();
			$total = BookProposition::where('status', 'approved')->where('owner_id', $user->id)->count();
		}
		return response()->json(['rows' => $propositions, 'total' => $total]);
	}

	public function rejected(Request $request) {
		$limit = $request->input('limit', 10);
		$offset = $request->input('offset', 0);
		$order = $request->input('order');
        if (!$order) {
            $order = 'asc';
        }
		$sort = $request->input('sort');
		$user = \Auth::user();
		if ($user->hasRole('access_all_propositions')) {
			$propositions = BookProposition::where( 'status', 'rejected' )->orderBy( $sort ? $sort : 'id', $order )->with( 'owner' )->limit( $limit )->offset( $offset )->get();
			$total = BookProposition::where('status', 'rejected')->count();
		}
		else {
			$propositions = BookProposition::where( 'status', 'rejected' )->where( 'owner_id', $user->id )->orderBy( $sort ? $sort : 'id', $order )->with( 'owner' )->limit( $limit )->offset( $offset )->get();
			$total = BookProposition::where('status', 'rejected')->where('owner_id', $user->id)->count();
		}
		return response()->json(['rows' => $propositions, 'total' => $total]);
	}

	public function deleted(Request $request) {
		$limit = $request->input('limit', 10);
		$offset = $request->input('offset', 0);
		$order = $request->input('order');
        if (!$order) {
            $order = 'asc';
        }
		$sort = $request->input('sort');
		$user = \Auth::user();
		if ($user->hasRole('access_all_propositions')) {
			$propositions = BookProposition::onlyTrashed()->orderBy( $sort ? $sort : 'id', $order )->with( 'owner' )->limit( $limit )->offset( $offset )->get();
			$total = BookProposition::onlyTrashed()->whereNotNull('deleted_at')->count();
		}
		else {
			$propositions = BookProposition::onlyTrashed()->where( 'owner_id', $user->id )->orderBy( $sort ? $sort : 'id', $order )->with( 'owner' )->limit( $limit )->offset( $offset )->get();
			$total = BookProposition::onlyTrashed()->where('owner_id', $user->id)->count();
		}
		return response()->json(['rows' => $propositions, 'total' => $total]);
	}

	public function archive(Request $request) {
		$limit = $request->input('limit', 10);
		$offset = $request->input('offset', 0);
		$order = $request->input('order');
        if (!$order) {
            $order = 'asc';
        }
		$sort = $request->input('sort');
		$user = \Auth::user();
		if ($user->hasRole('access_all_propositions')) {
			$propositions = BookProposition::where( 'status', 'archived' )->orderBy( $sort ? $sort : 'id', $order )->with( 'owner' )->limit( $limit )->offset( $offset )->get();
			$total = BookProposition::where('status', 'archived')->count();
		}
		else {
			$propositions = BookProposition::where( 'status', 'archived' )->where( 'owner_id', $user->id )->orderBy( $sort ? $sort : 'id', $order )->with( 'owner' )->limit( $limit )->offset( $offset )->get();
			$total = BookProposition::where('status', 'archived')->where('owner_id', $user->id)->count();
		}
		return response()->json(['rows' => $propositions, 'total' => $total]);
	}
}