<?php

namespace Inspirium\BookProposition\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inspirium\BookProposition\Models\BookProposition;
use Inspirium\BookProposition\Models\PropositionOption;

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
				$proposition->possible_products = $request->input('data.possible_products');
				$proposition->dotation = $request->input('data.dotation');
				$proposition->dotation_amount = $request->input('data.dotation_amount');
				$proposition->dotation_origin = $request->input('data.dotation_origin');
				$proposition->manuscript = $request->input('data.manuscript');
				break;
			case 'categorization':
				$proposition->supergroup_id = $request->input('data.supergroup');
				$proposition->upgroup_id = $request->input('data.upgroup');
				$proposition->group_id = $request->input('data.group');
				$proposition->book_type_group_id = $request->input('data.book_type_group');
				$proposition->book_type_id = $request->input('data.book_type');
				$proposition->school_type = $request->input('data.school_type');
				$proposition->school_level = $request->input('data.school_level');
				$proposition->school_assignment = $request->input('data.school_assignment');
				$proposition->school_subject_id = $request->input('data.school_subject');
				$proposition->school_subject_detailed_id = $request->input('data.school_subject_detailed');
				$proposition->biblioteca = $request->input('data.biblioteca');
				break;
			case 'market_potential':
				$proposition->main_target = $request->input('data.main_target');
				break;
			case 'technical_data':
				$proposition->number_of_pages = $request->input('data.number_of_pages');
				$proposition->width = $request->input('data.width');
				$proposition->height = $request->input('data.height');
				$proposition->paper_type = $request->input('data.paper_type');
				$proposition->additional_work = $request->input('data.additional_work');
				$proposition->colors = $request->input('data.colors');
				$proposition->colors_first_page = $request->input('data.colors_first_page');
				$proposition->cover_type = $request->input('data.cover_type');
				$proposition->cover_paper_type = $request->input('data.cover_paper_type');
				$proposition->cover_colors = $request->input('data.cover_colors');
				$proposition->cover_plastification = $request->input('data.cover_plastification');
				$proposition->film_print = $request->input('data.film_print');
				$proposition->blind_print = $request->input('data.blind_print');
				$proposition->uv_film = $request->input('data.uv_film');
				break;
			case 'print':
				foreach ($request->input('data.offers') as $offer_id => $offer) {
					$option = PropositionOption::find( $offer_id);
					unset($offer['note']);//TODO: temp fix
					if ($option) {
						$option->fill($offer);
					}
					else {
						$option = PropositionOption::create($offer);
					}
					$option->proposition_id = $id;
					$option->save();
				}
				break;
			case 'authors_expense':
				break;
			case 'production_expense':
				$proposition->text_price = $request->input('data.text_price');
				$proposition->text_price_amount = $request->input('data.text_price_amount');
				$proposition->accontation = $request->input('data.accontation');
				$proposition->netto_price_percentage = $request->input('data.netto_price_percentage');
				$proposition->reviews = $request->input('data.reviews');
				$proposition->lecture = $request->input('data.lecture');
				$proposition->lecture_amount = $request->input('data.lecture_amount');
				$proposition->correction = $request->input('data.correction');
				$proposition->correction_amount = $request->input('data.correction_amount');
				$proposition->proofreading = $request->input('data.proofreading');
				$proposition->proofreading_amount = $request->input('data.proofreading_amount');
				$proposition->translation = $request->input('data.translation');
				$proposition->translation_amount = $request->input('data.translation_amount');
				$proposition->index = $request->input('data.index');
				$proposition->index_amount = $request->input('data.index_amount');
				$proposition->epilogue = $request->input('data.epilogue');
				$proposition->photos = $request->input('data.photos');
				$proposition->photos_amount = $request->input('data.photos_amount');
				$proposition->illustrations = $request->input('data.illustrations');
				$proposition->illustrations_amount = $request->input('data.illustrations_amount');
				$proposition->technical_drawings = $request->input('data.technical_drawings');
				$proposition->technical_drawings_amount = $request->input('data.technical_drawings_amount');
				$proposition->expert_report = $request->input('data.expert_report');
				$proposition->copyright = $request->input('data.copyright');
				$proposition->copyright_mediator = $request->input('data.copyright_mediator');
				$proposition->selection = $request->input('data.selection');
				$proposition->powerpoint_presentation = $request->input('data.powerpoint_presentation');
				$proposition->methodical_instrumentarium = $request->input('data.methodical_instrumentarium');
				$proposition->additional_expense = $request->input('data.additional_expense');
				break;
			case 'marketing_expense':
				break;
			case 'distribution_expense':
				$proposition->margin = $request->input('data.margin');
				break;
			case 'layout_expense':
				$proposition->layout_complexity = $request->input('data.layout_complexity');
				$proposition->layout_include = $request->input('data.layout_include');
				$proposition->design_complexity = $request->input('data.design_complexity');
				$proposition->design_include = $request->input('data.design_include');
				$proposition->layout_note = $request->input('data.layout_note');
				$proposition->design_note = $request->input('data.design_note');
				break;
			case 'deadline':
				$proposition->deadline = $request->input('data.date');
				$proposition->priority = $request->input('data.priority');
				break;
			case 'price_sales':
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