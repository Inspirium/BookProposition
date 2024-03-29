<?php

namespace Inspirium\BookProposition\Controllers\Api;

use Carbon\Carbon;
use Inspirium\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inspirium\BookProposition\Models\AdditionalExpense;
use Inspirium\BookProposition\Models\AuthorExpense;
use Inspirium\BookProposition\Models\BookProposition;
use Inspirium\BookProposition\Models\MarketingExpense;
use Inspirium\BookProposition\Models\ProductionExpense;
use Inspirium\BookProposition\Models\PropositionNote;
use Inspirium\BookProposition\Models\PropositionOption;
use Inspirium\Models\BookManagement\Book;
use Inspirium\Models\BookManagement\BookTender;
use Inspirium\Models\FileManagement\File;
use Inspirium\Models\HumanResources\Employee;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;
use Unoconv\Unoconv;

class PropositionController extends Controller {

	public function getInitData( $id ) {
		$proposition       = BookProposition::withTrashed()->find( $id );
		$out               = [];
		if ($proposition) {
			$out['proposition_id']         = $proposition->id;
			$out['created_at'] = $proposition->created_at;
			$out['updated_at'] = $proposition->updated_at;
			$out['deleted_at'] = $proposition->deleted_at;
			$out['owner'] = $proposition->owner;
		}
		else {
			$out['owner'] = Auth::user();
		}

		return response()->json( $out );
	}

	public function initProposition( Request $request ) {
		$proposition                            = new BookProposition();
		$proposition->project_name              = $request->input( 'project_name' );
		$proposition->project_number            = $request->input( 'project_number' );
		$proposition->additional_project_number = $request->input( 'additional_project_number' );
		$proposition->status                    = 'unfinished';
		$employee = \Auth::user();
		$proposition->owner()->associate($employee);
		$proposition->save();
		$this->setNote( $proposition, $request->input( 'note' ), 'start' );
		if (!$proposition->productionExpenses->count()) {
			$expense1 = ProductionExpense::create(['type' => 'budget', 'proposition_id' => $proposition->id]);
			$expense2 = ProductionExpense::create(['type' => 'expense', 'proposition_id' => $proposition->id, 'parent_id' => $expense1->id]);
		}
		if (!$proposition->marketingExpenses->count()) {
			$expense1 = MarketingExpense::create(['type' => 'budget', 'proposition_id' => $proposition->id]);
			$expense2 = MarketingExpense::create(['type' => 'expense', 'proposition_id' => $proposition->id, 'parent_id' => $expense1->id]);
		}

		return response()->json( [
			'proposition_id' => $proposition->id,
			'owner' => $proposition->owner,
			'created_at' => $proposition->created_at
			] );
	}

	public function getPropositionStep( $id, $step, $type = null ) {
		$user = Auth::user();
		$proposition   = BookProposition::withTrashed()->with(['editors' => function($query) use ($user, $step) {
			$query->wherePivot('employee_id', $user->id)->where(function ($query) use ($step) {
				$query->where('pivot_proposition_user_tasks.step', $step)->orWhere('pivot_proposition_user_tasks.complete', true);
			});
		}])->find( $id );
		if (!$proposition) {
			return response()->json(['error' => 'no proposition found'], 404);
		}

		if (!$user->can('view', $proposition) && !count($proposition->editors)) {
			return response()->json(['error' => 'not authorized'], 403);
		}

		$allowed_steps = [
			'basic_data',
			'translation',
			'start',
			'categorization',
			'market_potential',
			'technical_data',
			'print',
			'authors_expense',
			'production_expense',
			'marketing_expense',
			'distribution_expense',
			'layout_expense',
			'deadline',
			'compare',
			'calculation',
			'price_definition'
		];

		$out           = [];
		if ( in_array( $step, $allowed_steps ) ) {
			$function = 'get' . str_replace( ' ', '', ucfirst( str_replace( '_', ' ', $step ) ) );
			if (strpos($function, 'expense')) {
				if (!$type) {
					$type = 'budget';
				}
				$out = $this->$function( $proposition, $type );
			}
			else {
				$out = $this->$function( $proposition );
			}
		}

		if (is_array($out)) {

			$out['proposition_id'] = $proposition->id;
			$out['created_at']     = $proposition->created_at;
			$out['updated_at']     = $proposition->updated_at;
			$out['deleted_at']     = $proposition->deleted_at;
			$out['owner']          = $proposition->owner;
			$out['type']           = $type;

			return response()->json( $out );
		}
		else {
			return $out;
		}
	}

	public function setPropositionStep( Request $request, $id, $step, $type = null ) {
		$user = Auth::user();
		$proposition   = BookProposition::withTrashed()->with(['editors' => function($query) use ($user, $step) {
			$query->wherePivot('employee_id', $user->id)->where(function ($query) use ($step) {
				$query->where('pivot_proposition_user_tasks.step', $step)->orWhere('pivot_proposition_user_tasks.complete', true);
			});
		}])->find( $id );
		if (!$proposition) {
			return response()->json(['error' => 'no proposition found'], 404);
		}

		if (!$user->can('update', $proposition) && !count($proposition->editors)) {
			return response()->json(['error' => 'not authorized'], 403);
		}
		$allowed_steps = [
			'basic_data',
			'translation',
			'start',
			'categorization',
			'market_potential',
			'technical_data',
			'print',
			'authors_expense',
			'production_expense',
			'marketing_expense',
			'distribution_expense',
			'layout_expense',
			'deadline',
			'compare',
			'calculation',
			'price_definition'
		];
		$out           = [];
		if ( in_array( $step, $allowed_steps ) ) {
			$function = 'set' . str_replace( ' ', '', ucfirst( str_replace( '_', ' ', $step ) ) );

			if (strpos($function, 'expense')) {
				if (!$type) {
					$type = 'budget';
				}
				$out = $this->$function( $request, $proposition, $type );
			}
			else {
				$out      = $this->$function( $request, $proposition );
			}
		}
		$out['proposition_id'] = $proposition->id;
		$out['type']      = $type;
		$status = $request->input('step_status');
		if (!$status) {
			$status = 'saved';
		}
		//$this->saveStepStatus($proposition, $step, $status);
		return response()->json( $out );
	}

	private function getStart( BookProposition $proposition ) {
		return [
			'project_number'            => $proposition->project_number,
			'project_name'              => $proposition->project_name,
			'additional_project_number' => $proposition->additional_project_number,
			'note'                      => $this->getNote( $proposition, 'start' ),
			'status' => $proposition->status
		];
	}

	private function setStart( Request $request, BookProposition $proposition ) {
		$proposition->project_number            = $request->input( 'project_number' );
		$proposition->project_name              = $request->input( 'project_name' );
		$proposition->additional_project_number = $request->input( 'additional_project_number' );
		if (!$proposition->status) {
			$proposition->status = 'unfinished';
		}
		if (!$proposition->owner) {
            $employee = Auth::user();
            $proposition->owner()->associate($employee);
        }
		$this->setNote( $proposition, $request->input( 'note' ), 'start' );
		$proposition->save();
		if (!$proposition->productionExpenses->count()) {
			$expense1 = ProductionExpense::create(['type' => 'budget', 'proposition_id' => $proposition->id]);
			$expense2 = ProductionExpense::create(['type' => 'expense', 'proposition_id' => $proposition->id, 'parent_id' => $expense1->id]);
		}
		if (!$proposition->marketingExpenses->count()) {
			$expense1 = MarketingExpense::create(['type' => 'budget', 'proposition_id' => $proposition->id]);
			$expense2 = MarketingExpense::create(['type' => 'expense', 'proposition_id' => $proposition->id, 'parent_id' => $expense1->id]);
		}

		return $this->getStart($proposition);
	}

	private function setNote( BookProposition $proposition, $text, $type ) {
		$note = $proposition->notes()->where( 'type', '=', $type )->first();
		if ( ! $note ) {
			$note = new PropositionNote( [
				'type'           => $type,
				'proposition_id' => $proposition->id
			] );
		}
		$note->note = $text;
		$note->save();
	}

	private function getNote( BookProposition $proposition, $type ) {
		$note = $proposition->notes()->where( 'type', '=', $type )->first();
		if ( $note ) {
			return $note->note;
		}

		return '';
	}

	/**
	 * @param BookProposition $proposition
	 *
	 * @return array
	 */
	private function getBasicData( BookProposition $proposition ) {
		return [
			'title'                => $proposition->title,
			'authors'              => $proposition->authors()->get(),
			'concept'              => $proposition->concept,
			'possible_products'    => $proposition->possible_products,
			'dotation'             => $proposition->dotation,
			'dotation_amount'      => $proposition->dotation_amount,
			'dotation_origin'      => $proposition->dotation_origin,
			'manuscript'           => $proposition->manuscript,
			'manuscript_documents' => $proposition->documents()->wherePivot( 'type', 'manuscript' )->get(),
			'questionnaire' => $proposition->documents()->wherePivot('type', 'questionnaire')->get(),
			'note'                 => $this->getNote( $proposition, 'basic_data' )
		];
	}

	private function setBasicData( Request $request, BookProposition $proposition ) {
		$proposition->title             = $request->input( 'title' );
		$proposition->concept           = $request->input( 'concept' );
		$proposition->possible_products = $request->input( 'possible_products' );
		$proposition->dotation          = $request->input( 'dotation' );
		$proposition->dotation_amount   = $request->input( 'dotation_amount' );
		$proposition->dotation_origin   = $request->input( 'dotation_origin' );
		$proposition->manuscript        = $request->input( 'manuscript'
		);

		foreach ( $request->input( 'manuscript_documents' ) as $document ) {
			$file        = File::find( $document['id'] );
			$file->title = $document['title'];
			$file->save();
			if ( ! $proposition->documents()->wherePivot( 'type', 'manuscript' )->get()->contains( $document['id'] ) ) {
				$proposition->documents()->save( $file, [ 'type' => 'manuscript' ] );
			}
		}
		foreach ( $request->input( 'questionnaire' ) as $document ) {
			$file        = File::find( $document['id'] );
			$file->title = $document['title'];
			$file->save();
			if ( ! $proposition->documents()->wherePivot( 'type', 'questionnaire' )->get()->contains( $document['id'] ) ) {
				$proposition->documents()->save( $file, [ 'type' => 'questionnaire' ] );
			}
		}
		$authors  = [];
		foreach ( $request->input( 'authors' ) as $author ) {
			$authors[] = $author['id'];
			if ( ! $proposition->authorExpenses->contains( $author['id'] ) ) {
				$expense1 = AuthorExpense::create(['author_id' => $author['id'], 'type' => 'budget']);
				$expense2 = AuthorExpense::create(['author_id' => $author['id'], 'type' => 'expense', 'parent_id' => $expense1->id]);
				$proposition->authorExpenses()->saveMany([
					$expense1, $expense2
				]);
			}
		}
		foreach ( $proposition->authorExpenses->groupBy('author_id') as $author_id => $expenses ) {
			if ( ! in_array( $author_id, $authors ) ) {
				/** @var AuthorExpense $expens */
				foreach ($expenses as $expens) {
					$expens->delete();
				}
			}
		}
		$proposition->authors()->sync( $authors );
		$proposition->save();
		$this->setNote( $proposition, $request->input( 'note' ), 'basic_data' );
		return $this->getBasicData($proposition);
	}

	private function getCategorization( BookProposition $proposition ) {
		return [
			'group'             => $proposition->bookCategories()->with( 'parent' )->first(),
			'book_type'         => $proposition->bookTypes()->first(),
			'school_type'       => $proposition->schoolTypes->pluck('id'),
			'school_level'      => $proposition->school_level,
			'school_assignment' => $proposition->school_assignment,
			'school_subject'    => $proposition->schoolSubjects()->first(),
			'biblioteca'        => $proposition->bibliotecas()->first(),
            'book_tender'       => $proposition->bookTenders()->first(),
			'note'              => $this->getNote( $proposition, 'categorization' )
		];
	}

	private function setCategorization( Request $request, BookProposition $proposition ) {
		$proposition->bookCategories()->sync( $request->input( 'group' ) );
		$proposition->bookTypes()->sync( $request->input( 'book_type' ) );
		$proposition->schoolTypes()->sync( $request->input( 'school_type' ) );
		$proposition->schoolSubjects()->sync( $request->input( 'school_subject_detailed' ) );
		$proposition->school_level      = $request->input( 'school_level' );
		$proposition->school_assignment = $request->input( 'school_assignment' );
		$proposition->bookTenders()->sync($request->input('book_tender'));
		$proposition->bibliotecas()->sync( $request->input( 'biblioteca' ) );
		$proposition->save();
		$this->setNote( $proposition, $request->input( 'note' ), 'categorization' );
		return $this->getCategorization($proposition);
	}

	private function getMarketPotential( BookProposition $proposition ) {
		return [
			'main_target'                => $proposition->main_target,
			'market_potential_documents' => $proposition->documents()->wherePivot( 'type', 'market_potential' )->get(),
			'note'                       => $this->getNote( $proposition, 'market_potential' )
		];
	}

	private function setMarketPotential( Request $request, BookProposition $proposition ) {
		$proposition->main_target = $request->input( 'main_target' );
		foreach ( $request->input( 'market_potential_documents' ) as $document ) {
			$file        = File::find( $document['id'] );
			$file->title = $document['title'];
			$file->save();
			if ( ! $proposition->documents()->wherePivot( 'type', 'market_potential' )->get()->contains( $document['id'] ) ) {
				$proposition->documents()->save( $file, [ 'type' => 'market_potential' ] );
			}
		}
		$proposition->save();
		$this->setNote( $proposition, $request->input( 'note' ), 'market_potential' );
		return $this->getMarketPotential($proposition);
	}

	private function getTechnicalData( BookProposition $proposition ) {
		return [
			'additions'            => $proposition->additions,
			'circulations'         => $proposition->circulations,
			'number_of_pages'      => $proposition->number_of_pages,
			'width'                => $proposition->width,
			'height'               => $proposition->height,
			'paper_type'           => $proposition->paper_type,
			'book_binding'         => $proposition->book_binding,
			'additional_work'      => $proposition->additional_work,
			'colors'               => $proposition->colors,
			'colors_first_page'    => $proposition->colors_first_page,
			'colors_last_page'     => $proposition->colors_last_page,
			'cover_type'           => $proposition->cover_type,
			'cover_paper_type'     => $proposition->cover_paper_type,
			'cover_colors'         => $proposition->cover_colors,
			'cover_plastification' => $proposition->cover_plastification,
			'film_print'           => $proposition->film_print,
			'blind_print'          => $proposition->blind_print,
			'uv_print'             => $proposition->uv_print,
			'coverpaper_paper_type'     => $proposition->coverpaper_paper_type,
			'coverpaper_colors'         => $proposition->coverpaper_colors,
			'coverpaper_plastification' => $proposition->coverpaper_plastification,
			'coverpaper_film_print'           => $proposition->coverpaper_film_print,
			'coverpaper_blind_print'          => $proposition->coverpaper_blind_print,
			'coverpaper_uv_print'             => $proposition->coverpaper_uv_print,
			'note'                 => $this->getNote( $proposition, 'technical_data' )
		];
	}

	private function setTechnicalData( Request $request, BookProposition $proposition ) {
		$proposition->number_of_pages      = $request->input( 'number_of_pages' );
		$proposition->width                = $request->input( 'width' );
		$proposition->height               = $request->input( 'height' );
		$proposition->paper_type           = $request->input( 'paper_type' );
		$proposition->additional_work      = $request->input( 'additional_work' );
		$proposition->colors               = $request->input( 'colors' );
		$proposition->colors_first_page    = $request->input( 'colors_first_page' );
		$proposition->colors_last_page     = $request->input( 'colors_last_page' );
		$proposition->cover_type           = $request->input( 'cover_type' );
		$proposition->cover_paper_type     = $request->input( 'cover_paper_type' );
		$proposition->cover_colors         = $request->input( 'cover_colors' );
		$proposition->cover_plastification = $request->input( 'cover_plastification' );
		$proposition->film_print           = $request->input( 'film_print' )==='yes';
		$proposition->blind_print          = $request->input( 'blind_print' )==='yes';
		$proposition->uv_print             = $request->input( 'uv_print' );
		$proposition->additions            = $request->input( 'additions' );

		$proposition->book_binding = $request->input( 'book_binding' );
		$proposition->coverpaper_paper_type = $request->input('coverpaper_paper_type');
		$proposition->coverpaper_colors = $request->input('coverpaper_colors');
		$proposition->coverpaper_plastification = $request->input('coverpaper_plastification');
		$proposition->coverpaper_uv_print = $request->input('coverpaper_uv_print');
		$proposition->coverpaper_blind_print = $request->input('coverpaper_blind_print');
		$proposition->coverpaper_film_print = $request->input('coverpaper_film_print');
		$circs                     = [];
		foreach ( $request->input( 'circulations' ) as $circulation ) {
			$option = PropositionOption::findOrNew( $circulation['id'] );
			$option->title = $circulation['title'];
			//$option->proposition_id = $id;
			$option->cover_type                = $request->input( 'cover_type' );
			$option->cover_paper_type          = $request->input( 'cover_paper_type' );
			$option->cover_colors              = $request->input( 'cover_colors' );
			$option->cover_plastification      = $request->input( 'cover_plastification' );
			$option->film_print                = $request->input( 'film_print' );
			$option->uv_print                  = $request->input( 'uv_print' );
			$option->blind_print               = $request->input( 'blind_print' );
			$option->colors                    = $request->input( 'colors' );
			$option->paper_type                = $request->input( 'paper_type' );
			$option->hard_cover_circulation    = $request->input( 'hard_cover_circulation' );
			$option->soft_cover_circulation    = $request->input( 'soft_cover_circulation' );
			$option->book_binding              = $request->input( 'book_binding' );
			$option->colors_first_page         = $request->input( 'colors_first_page' );
			$option->colors_last_page          = $request->input( 'color_last_page' );
			$option->number_of_pages           = $request->input( 'number_of_pages' );
			$option->coverpaper_paper_type = $request->input('coverpaper_paper_type');
			$option->coverpaper_colors = $request->input('coverpaper_colors');
			$option->coverpaper_plastification = $request->input('coverpaper_plastification');
			$option->coverpaper_uv_print = $request->input('coverpaper_uv_print');
			$option->coverpaper_blind_print = $request->input('coverpaper_blind_print');
			$option->coverpaper_film_print = $request->input('coverpaper_film_print');
			$option->calculated_profit_percent = 18;
			$option->shop_percent              = 20;
			$option->vat_percent               = 5;
			$option->save();
			$proposition->options()->save( $option );
			$circs[] = $option->id;
		}
		/** @var PropositionOption $option */
		$out = [];
		foreach ( $proposition->options as $option ) {
			if ( ! in_array( $option->id, $circs ) ) {
				$option->delete();
			} else {
				$out[] = [ 'id' => $option->id, 'title' => $option->title ];
			}
		}
		$proposition->save();
		$this->setNote( $proposition, $request->input( 'note' ), 'technical_data' );

		return [ 'circulations' => $out ];
	}

	private function getPrint( BookProposition $proposition ) {
		if (count($proposition->offers)) {
			return [
				'offers' => $proposition->offers,
				'note'   => $this->getNote( $proposition, 'print' )
			];
		}
		else {
			return response()->json(['error' => 'data missing', 'message' => __('You need to enter circulations under section *technical data* in order to enable this screen')], 412);
		}
	}

	private function setPrint( Request $request, BookProposition $proposition ) {
		$circulations = [];
		foreach ( $request->input( 'offers' ) as $offer_id => $offer ) {
			$option = PropositionOption::find( $offer_id );
			if ( ! $option ) {
				continue;
			}
			$option->mapModel( $offer );
			$option->save();
			$circulations[] = [
				'title' => $option->title,
				'id'    => $option->id
			];
			foreach ( $offer['files'] as $document ) {
				$file        = File::find( $document['id'] );
				$file->title = $document['title'];
				$file->save();
				if ( ! $option->files()->get()->contains( $document['id'] ) ) {
					$option->files()->save( $file, ['type'=> 'proposition_option'] );
				}
			}
		}
		$this->setNote( $proposition, $request->input( 'note' ), 'print' );
		$this->getPrint($proposition);
	}

	private function getAuthorsExpense( BookProposition $proposition, $type ) {
		if (!$type) {
			$type = 'budget';
		}
		$out = [
			'authors' => $proposition->authors()->with( [
				'expenses' => function ( $query ) use ( $type, $proposition ) {
					$query->where('type', $type)->where('proposition_id', $proposition->id);
				}
			] )->get()->keyBy( 'id' ),
			'other'   => $proposition->authorOtherExpenses()->where('type', '=', 'author_other_expense_'.$type)->get(),
			'note'    => $this->getNote( $proposition, 'authors_expense_'.$type ),

		];
		return $out;
	}

	private function setAuthorsExpense( Request $request, BookProposition $proposition, $type ) {
		if (!$type) {
			$type = 'budget';
		}
		foreach ( $request->input( 'authors' ) as $author ) {
			$expense = $author['expenses'][0];
			$e       = AuthorExpense::find( $expense['id'] );
			$e->fill( [
				'amount'              => $expense['amount'],
				'accontation'         => $expense['accontation'],
				'percentage'          => $expense['percentage'],
				'type' => $type
			] );
			$e->save();
			$additional = [];
			foreach ($expense['additional_expenses'] as $ae) {
				if (isset($ae['id']) && $ae['id']) {
					$a = AdditionalExpense::find( $ae['id'] );
				}
				else {
					$a = new AdditionalExpense();
				}
				$a->expense = $ae['expense'];
				$a->amount = $ae['amount'];
				$a->save();
				$additional[] = $a->id;
				$e->additionalExpenses()->save($a);
			}
			if ($e->additionalExpenses && $additional) {
				foreach ( $e->additionalExpenses as $ae ) {
					if ( ! in_array( $ae->id, $additional ) ) {
						$ae->delete();
					}
				}
			}
		}
		$other_expenses = [];
		foreach($request->input( 'other' ) as $other) {
			if (isset($other['id']) && $other['id']) {
				$o = AdditionalExpense::find($other['id']);
			}
			else {
				$o = new AdditionalExpense();
			}
			$o->expense = $other['expense'];
			$o->amount = $other['amount'];
			$o->type = 'author_other_expense_'.$type;
			$o->save();
			$other_expenses[] = $o->id;
			$proposition->authorOtherExpenses()->save($o);
		}
		if ($proposition->authorOtherExpenses && $other_expenses) {
			foreach ( $proposition->authorOtherExpenses()->where('type', 'author_other_expense_'. $type)->get() as $o ) {
				if ( ! in_array( $o->id, $other_expenses ) ) {
					$o->delete();
				}
			}
		}
		$proposition->save();
		$this->setNote( $proposition, $request->input( 'note' ), 'authors_expense_'.$type );

		return $this->getAuthorsExpense($proposition, $type);
	}

	private function getProductionExpense( BookProposition $proposition, $type ) {
		if (!$type) {
			$type = 'budget';
		}
		$out = $proposition->productionExpenses()->where('type', '=', $type)->first();
		$out['note'] = $this->getNote( $proposition, 'production_expense' );
		return $out;
	}

	private function setProductionExpense( Request $request, BookProposition $proposition, $type ) {
		if (!$type) {
			$type = 'budget';
		}
		/** @var ProductionExpense $expense */
		$expense = $proposition->productionExpenses()->where('type', '=', $type)->first();
		$expense->text_price                    = $request->input( 'text_price' );
		$expense->text_price_amount             = $request->input( 'text_price_amount' );
		$expense->accontation                   = $request->input( 'accontation' );
		$expense->netto_price_percentage        = $request->input( 'netto_price_percentage' );
		$expense->reviews                       = $request->input( 'reviews' );
		$expense->lecture                       = $request->input( 'lecture' );
		$expense->lecture_amount                = $request->input( 'lecture_amount' );
		$expense->correction                    = $request->input( 'correction' );
		$expense->correction_amount             = $request->input( 'correction_amount' );
		$expense->proofreading                  = $request->input( 'proofreading' );
		$expense->proofreading_amount           = $request->input( 'proofreading_amount' );
		$expense->translation                   = $request->input( 'translation' );
		$expense->translation_amount            = $request->input( 'translation_amount' );
		$expense->index                         = $request->input( 'index' );
		$expense->index_amount                  = $request->input( 'index_amount' );
		$expense->epilogue                      = $request->input( 'epilogue' );
		$expense->photos                        = $request->input( 'photos' );
		$expense->photos_amount                 = $request->input( 'photos_amount' );
		$expense->illustrations                 = $request->input( 'illustrations' );
		$expense->illustrations_amount          = $request->input( 'illustrations_amount' );
		$expense->technical_drawings            = $request->input( 'technical_drawings' );
		$expense->technical_drawings_amount     = $request->input( 'technical_drawings_amount' );
		$expense->expert_report                 = $request->input( 'expert_report' );
		$expense->copyright                     = $request->input( 'copyright' );
		$expense->copyright_mediator            = $request->input( 'copyright_mediator' );
		$expense->selection                     = $request->input( 'selection' );
		$expense->powerpoint_presentation       = $request->input( 'powerpoint_presentation' );
		$expense->methodical_instrumentarium    = $request->input( 'methodical_instrumentarium' );

		$other_expenses = [];
		foreach($request->input( 'additional_expenses' ) as $other) {
			if (isset($other['id']) && $other['id']) {
				$o = AdditionalExpense::find($other['id']);
			}
			else {
				$o = new AdditionalExpense();
			}
			$o->expense = $other['expense'];
			$o->amount = $other['amount'];
			$o->save();
			$other_expenses[] = $o->id;
			$expense->additionalExpenses()->save($o);
		}
		if ($expense->additionalExpenses && $other_expenses) {
			foreach ( $expense->additionalExpenses as $o ) {
				if ( ! in_array( $o->id, $other_expenses ) ) {
					$o->delete();
				}
			}
		}

		$this->setNote( $proposition, $request->input( 'note' ), 'production_expense' );
		$expense->save();

		return $this->getProductionExpense($proposition, $type);
	}

	private function getMarketingExpense( BookProposition $proposition, $type ) {
		if (!$type) {
			$type = 'budget';
		}
		$out = $proposition->marketingExpenses()->where('type', '=', $type)->first();
		$out['note'] = $this->getNote( $proposition, 'marketing_expense' );
		return $out;
	}

	private function setMarketingExpense( Request $request, BookProposition $proposition, $type ) {
		if (!$type) {
			$type = 'budget';
		}
		$expense = $proposition->marketingExpenses()->where('type', '=', $type)->first();
		$expense->expense  = $request->input( 'expense' );
		$other_expenses = [];
		foreach($request->input( 'additional_expenses' ) as $other) {
			if (isset($other['id']) && $other['id']) {
				$o = AdditionalExpense::find($other['id']);
			}
			else {
				$o = new AdditionalExpense();
			}
			$o->expense = $other['expense'];
			$o->amount = $other['amount'];
			$o->save();
			$other_expenses[] = $o->id;
			$expense->additionalExpenses()->save($o);
		}
		if ($expense->additionalExpenses && $other_expenses) {
			foreach ( $expense->additionalExpenses as $o ) {
				if ( ! in_array( $o->id, $other_expenses ) ) {
					$o->delete();
				}
			}
		}
		$expense->save();
		$this->setNote( $proposition, $request->input( 'note' ), 'marketing_expense' );
		$proposition->save();
		return $this->getMarketingExpense($proposition, $type);
	}

	private function getDistributionExpense( BookProposition $proposition, $type ) {
		$expense = $proposition->productionExpenses()->where('type', '=', $type)->first();
		return [
			'margin' => $expense->distribution_margin,
			'note'   => $this->getNote( $proposition, 'distribution_expense_'.$type )
		];
	}

	private function setDistributionExpense( Request $request, BookProposition $proposition, $type ) {
		$expense = $proposition->productionExpenses()->where('type', '=', $type)->first();
		$expense->margin = $request->input( 'margin' );
		$this->setNote( $proposition, $request->input( 'note' ), 'distribution_expense_'.$type );
		$expense->save();
		return $this->getDistributionExpense($proposition, $type);
	}

	private function getLayoutExpense( BookProposition $proposition, $type ) {
		$expense = $proposition->productionExpenses()->where('type' , '=', $type)->first();
		$group = $proposition->bookCategories()->with( 'parent.parent' )->first();
		if (!$group) {
			return response()->json(['error' => 'data missing', 'message' => __('You need to select the book group in order to enable this screen')], 412);
		}
		return [
			'layout_complexity'         => $expense->layout_complexity,
			'layout_include'            => $expense->layout_include,
			'layout_note'               => $this->getNote( $proposition, 'layout_note_' . $type ),
			'layout_exact_price'        => $expense->layout_exact_price,
			'design_complexity'         => $expense->design_complexity,
			'design_include'            => $expense->design_include,
			'design_note'               => $this->getNote( $proposition, 'design_note_' . $type ),
			'design_exact_price'        => $expense->design_exact_price,
			'number_of_pages'           => $proposition->number_of_pages,
			'photos_amount'             => $expense->photos_amount,
			'illustrations_amount'      => $expense->illustrations_amount,
			'technical_drawings_amount' => $expense->technical_drawings_amount,
			'group'                     => $group,
		];
	}

	private function setLayoutExpense( Request $request, BookProposition $proposition, $type ) {
		$expense = $proposition->productionExpenses()->where('type' , '=', $type)->first();
		$expense->layout_complexity = $request->input( 'layout_complexity' );
		$expense->layout_include    = $request->input( 'layout_include' );
		$expense->design_complexity = $request->input( 'design_complexity' );
		$expense->design_include    = $request->input( 'design_include' );
		$expense->layout_exact_price = $request->input('layout_exact_price');
		$expense->design_exact_price = $request->input('design_exact_price');
		$expense->save();
		$this->setNote( $proposition, $request->input( 'layout_note' ), 'layout_note_' . $type );
		$this->setNote( $proposition, $request->input( 'design_note' ), 'design_note_' . $type );
		return $this->getLayoutExpense($proposition, $type);
	}

	private function getDeadline( BookProposition $proposition ) {
		return [
			'date'     => $proposition->deadline,
			'priority' => $proposition->priority,
			'note'     => $this->getNote( $proposition, 'deadline' )
		];
	}

	private function getPriceDefinition(BookProposition $proposition) {
		$final = $proposition->options()->where('is_final', 1)->first();
		return [
			'price_first_year' => $proposition->price_first_year,
			'price_second_year' => $proposition->price_second_year,
			'retail_price' => $proposition->retail_price,
			'final_circulation' => $proposition->final_circulation,
			'final_print_price' => $proposition->final_print_price,
			'offers' => $proposition->options->mapWithKeys(function($option) {
				return [$option['id'] => $option['title']];
			}),
			'selected_circulation' => $final?$final->id:0,
			'print_offers' => $proposition->documents()->wherePivot('type', 'print_offers')->get()
		];
	}

	private function setPriceDefinition(Request $request, BookProposition $proposition) {
		$proposition->price_first_year = $request->input('price_first_year');
		$proposition->price_second_year = $request->input('price_second_year');
		$proposition->retail_price = $request->input('retail_price');
		$proposition->final_print_price = $request->input('final_print_price');
		$proposition->final_circulation = $request->input('final_circulation');
		$proposition->save();
		$offers = $proposition->options;
		$selected = $request->input('selected_circulation');
		foreach ($offers as $one) {
			if ($one->id == $selected) {
				$one->is_final = true;
			}
			else {
				$one->is_final = false;
			}
			$one->save();
		}

		foreach ( $request->input( 'print_offers' ) as $document ) {
			$file        = File::find( $document['id'] );
			$file->title = $document['title'];
			$file->save();
			if ( ! $proposition->documents()->wherePivot( 'type', 'print_offers' )->get()->contains( $document['id'] ) ) {
				$proposition->documents()->save( $file, [ 'type' => 'print_offers' ] );
			}
		}
	}

	private function setDeadline( Request $request, BookProposition $proposition ) {
		$proposition->deadline = $request->input( 'date' );
		$proposition->priority = $request->input( 'priority' );
		$this->setNote( $proposition, $request->input( 'note' ), 'deadline' );
		$proposition->save();

		return $this->getDeadline($proposition);
	}

	private function getCalculation( BookProposition $proposition ) {
		$authors = $proposition->authorExpenses()->where('type', '=', 'budget')->get();
		$authors_other = $authors->sum(function($expense) {
			return $expense->additionalExpenses->sum('amount');
		});
		$authors_advance = $authors->sum('accontation');
		$authors_total = $authors->sum('amount') + $authors_other + $proposition->authorOtherExpenses()->where('type', 'author_other_expense_budget')->get()->sum('amount');

		$production_expense = $proposition->productionExpenses()->where('type', '=', 'budget')->first();

		$marketing_expense = $proposition->marketingExpenses()->where('type', '=', 'budget')->first();

		return [
			'authors_total' => $authors_total,
			'authors_advance' => $authors_advance,
			'authors_other' => $authors_other,
			'author_expenses' => $authors,
			'offers' => $proposition->offers,
			'marketing_expense' => $marketing_expense->totals,
			'production_expense' => $production_expense->totals['total'],
			'design_layout_expense' => $production_expense->totals['layout'],
			'dotation' => $proposition->dotation_amount
		];
	}

	private function setCalculation(Request $request, BookProposition $proposition ) {
		foreach ($request->input('offers') as $offer) {
			$option = PropositionOption::find($offer['id']);
			unset($offer['files']);
			$option->fill($offer);
			$option->save();
		}
		$proposition->save();

		return $this->getCalculation($proposition);
	}

	private function getCompare( BookProposition $proposition ) {
		$marketing_expense = $proposition->marketingExpenses->keyBy('type');
		$production_expense = $proposition->productionExpenses->keyBy('type');
		$authors = $proposition->authors()->with(['expenses' => function($query) use ($proposition) {
			$query->where('proposition_id', $proposition->id);
		}])->get();
		$requests = $proposition->approvalRequests()->orderBy('updated_at', 'desc')->get()->groupBy('designation');
		$authors_other = [
			'budget' => $proposition->authorOtherExpenses()->where('type', '=', 'author_other_expense_budget')->get()->sum('amount'),
			'expense' => $proposition->authorOtherExpenses()->where('type', '=', 'author_other_expense_expense')->get()->sum('amount'),
		];
		return [
			'marketing_expense' => $marketing_expense,
			'production_expense' => $production_expense,
			'requests' => $requests,
			'authors' => $authors,
			'authors_other' => $authors_other
		];
	}

	private function setCompare( Request $request, $proposition ) {

	}

	public function getFiles( $id, $type ) {
		$user = Auth::user();
		$proposition   = BookProposition::withTrashed()->with(['editors' => function($query) use ($user, $type) {
			$query->wherePivot('employee_id', $user->id)->where(function ($query) use ($type) {
				$query->where('pivot_proposition_user_tasks.step', $type)->orWhere('pivot_proposition_user_tasks.complete', true);
			});
		}])->find( $id );
		if (!$proposition) {
			return response()->json(['error' => 'no proposition found'], 404);
		}

		if (!$user->can('view', $proposition) && !count($proposition->editors)) {
			return response()->json(['error' => 'not authorized'], 403);
		}

		return [
			'files' => $proposition->documents()->wherePivot( 'type', $type )->wherePivot( 'final', false )->get(),
			'final' => $proposition->documents()->wherePivot( 'type', $type )->wherePivot( 'final', true )->get(),
			'step_status' => isset($proposition->step_status[$type])?$proposition->step_status[$type]:''
		];
	}

	public function setFiles( Request $request, $id, $type ) {
		$user = Auth::user();
		$proposition   = BookProposition::withTrashed()->with(['editors' => function($query) use ($user, $type) {
			$query->wherePivot('employee_id', $user->id)->where(function ($query) use ($type) {
				$query->where('pivot_proposition_user_tasks.step', $type)->orWhere('pivot_proposition_user_tasks.complete', true);
			});
		}])->find( $id );
		if (!$proposition) {
			return response()->json(['error' => 'no proposition found'], 404);
		}
		if (!$user->can('update', $proposition) && !count($proposition->editors)) {
			return response()->json(['error' => 'not authorized'], 403);
		}
		if ($request->has('files')) {
			foreach ( $request->input( 'files' ) as $document ) {
				$file        = File::find( $document['id'] );
				$file->title = $document['title'];
				$file->save();
				if ( ! $proposition->documents()->wherePivot( 'type', $type )->get()->contains( $document['id'] ) ) {
					$proposition->documents()->save( $file, [ 'type' => $type ] );
				}
			}
			foreach ( $request->input( 'final' ) as $document ) {
				$file        = File::find( $document['id'] );
				$file->title = $document['title'];
				$file->save();
				if ( ! $proposition->documents()->wherePivot( 'type', $type )->get()->contains( $document['id'] ) ) {
					$proposition->documents()->save( $file, [
						'type'  => $type,
						'final' => true
					] );
				}
			}
			$this->saveStepStatus($proposition, $type, 'uploaded');
		}
		if ($request->has('status')) {
			$this->saveStepStatus($proposition, $type, $request->input('status'));
		}
		return $this->getFiles($id, $type);
	}

	public function getMultimedia($id) {
		$user = Auth::user();
		$proposition   = BookProposition::withTrashed()->with(['editors' => function($query) use ($user) {
			$query->wherePivot('employee_id', $user->id)->where(function ($query) {
				$query->where('pivot_proposition_user_tasks.step', 'multimedia')->orWhere('pivot_proposition_user_tasks.complete', true);
			});
		}])->find( $id );
		if (!$proposition) {
			return response()->json(['error' => 'no proposition found'], 404);
		}

		if (!$user->can('view', $proposition) && !count($proposition->editors)) {
			return response()->json(['error' => 'not authorized'], 403);
		}

		return [
			'webshop' => $this->getNote($proposition, 'webshop'),
			'jpg' => $proposition->documents()->wherePivot( 'type', 'multimedia.jpg' )->get(),
			'psd' => $proposition->documents()->wherePivot( 'type', 'multimedia.psd' )->get(),
			'preview' => $proposition->documents()->wherePivot( 'type', 'multimedia.preview' )->get(),
			'step_status' => isset($proposition->step_status['multimedia'])?$proposition->step_status['multimedia']:''
		];
	}

	public function setMultimedia(Request $request, $id) {
		$user = Auth::user();
		$proposition   = BookProposition::withTrashed()->with(['editors' => function($query) use ($user) {
			$query->wherePivot('employee_id', $user->id)->where(function ($query) {
				$query->where('pivot_proposition_user_tasks.step', 'multimedia')->orWhere('pivot_proposition_user_tasks.complete', true);
			});
		}])->find( $id );
		if (!$proposition) {
			return response()->json(['error' => 'no proposition found'], 404);
		}

		if (!$user->can('update', $proposition) && !count($proposition->editors)) {
			return response()->json(['error' => 'not authorized'], 403);
		}
		if ($request->has('status')) {
			$this->saveStepStatus($proposition, 'multimedia', $request->input('status'));
		}
		else {
			$this->setNote( $proposition, $request->input( 'webshop' ), 'webshop' );
			foreach ( $request->input( 'jpg' ) as $document ) {
				$file        = File::find( $document['id'] );
				$file->title = $document['title'];
				$file->save();
				if ( ! $proposition->documents()->wherePivot( 'type', 'multimedia.jpg' )->get()->contains( $document['id'] ) ) {
					$proposition->documents()->save( $file, [ 'type' => 'multimedia.jpg' ] );
				}
			}
			foreach ( $request->input( 'psd' ) as $document ) {
				$file        = File::find( $document['id'] );
				$file->title = $document['title'];
				$file->save();
				if ( ! $proposition->documents()->wherePivot( 'type', 'multimedia.psd' )->get()->contains( $document['id'] ) ) {
					$proposition->documents()->save( $file, [
						'type' => 'multimedia.psd'
					] );
				}
			}
			foreach ( $request->input( 'preview' ) as $document ) {
				$file        = File::find( $document['id'] );
				$file->title = $document['title'];
				$file->save();
				if ( ! $proposition->documents()->wherePivot( 'type', 'multimedia.preview' )->get()->contains( $document['id'] ) ) {
					$proposition->documents()->save( $file, [
						'type' => 'multimedia.preview'
					] );
				}
			}
		}
		return $this->getMultimedia($id);
	}

	public function getMarketing($id) {
		$user = Auth::user();
		$proposition   = BookProposition::withTrashed()->with(['editors' => function($query) use ($user) {
			$query->wherePivot('employee_id', $user->id)->where(function ($query) {
				$query->where('pivot_proposition_user_tasks.step', 'marketing')->orWhere('pivot_proposition_user_tasks.complete', true);
			});
		}])->find( $id );
		if (!$proposition) {
			return response()->json(['error' => 'no proposition found'], 404);
		}

		if (!$user->can('view', $proposition) && !count($proposition->editors)) {
			return response()->json(['error' => 'not authorized'], 403);
		}

		return [
			'cover' => $proposition->documents()->wherePivot( 'type', 'marketing.cover' )->get(),
			'leaflet' => $proposition->documents()->wherePivot( 'type', 'marketing.leaflet' )->get(),
			'step_status' => isset($proposition->step_status['marketing'])?$proposition->step_status['marketing']:''
		];
	}

	public function setMarketing(Request $request, $id) {
		$user = Auth::user();
		$proposition   = BookProposition::withTrashed()->with(['editors' => function($query) use ($user) {
			$query->wherePivot('employee_id', $user->id)->where(function ($query) {
				$query->where('pivot_proposition_user_tasks.step', 'marketing')->orWhere('pivot_proposition_user_tasks.complete', true);
			});
		}])->find( $id );
		if (!$proposition) {
			return response()->json(['error' => 'no proposition found'], 404);
		}

		if (!$user->can('update', $proposition) && !count($proposition->editors)) {
			return response()->json(['error' => 'not authorized'], 403);
		}
		if ($request->has('status')) {
			$this->saveStepStatus($proposition, 'marketing', $request->input('status'));
		}
		else {
			foreach ( $request->input( 'cover' ) as $document ) {
				$file        = File::find( $document['id'] );
				$file->title = $document['title'];
				$file->save();
				if ( ! $proposition->documents()->wherePivot( 'type', 'marketing.cover' )->get()->contains( $document['id'] ) ) {
					$proposition->documents()->save( $file, [ 'type' => 'marketing.cover' ] );
				}
			}
			foreach ( $request->input( 'leaflet' ) as $document ) {
				$file        = File::find( $document['id'] );
				$file->title = $document['title'];
				$file->save();
				if ( ! $proposition->documents()->wherePivot( 'type', 'marketing.leaflet' )->get()->contains( $document['id'] ) ) {
					$proposition->documents()->save( $file, [
						'type' => 'marketing.leaflet'
					] );
				}
			}
		}
		return $this->getMarketing($id);
	}

	public function getOfferDoc($id, $offer_id, $doc_type) {
		$proposition = BookProposition::find($id);
		$offer = PropositionOption::find($offer_id);
		$formatted = $offer->getFormattedStrings();
		$templateProcessor = new TemplateProcessor(resource_path('print_offer_template.docx'));
		$templateProcessor->setValue('project_name', $proposition->title);
		$templateProcessor->setValue('circulation', $offer->title);
		$templateProcessor->setValue('paper_type', $offer->paper_type);
		$templateProcessor->setValue('book_binding', $formatted['book_binding']);
		$templateProcessor->setValue('colors', $formatted['colors']);
		$templateProcessor->setValue('colors_first_page', $formatted['colors_first_page']);
		$templateProcessor->setValue('colors_last_page', $formatted['colors_last_page']);
		$templateProcessor->setValue('additional_work', $offer->additional_work);
		$templateProcessor->setValue('cover_type', $formatted['cover_type']);
		$templateProcessor->setValue('hard_cover_circulation', $offer->hard_cover_circulation);
		$templateProcessor->setValue('soft_cover_circulation', $offer->soft_cover_circulation);
		$templateProcessor->setValue('cover_paper_type', $offer->cover_paper_type);
		$templateProcessor->setValue('cover_colors', $formatted['cover_colors']);
		$templateProcessor->setValue('cover_plastification', $formatted['plastification']);
		$templateProcessor->setValue('film_print', $formatted['film_print']);
		$templateProcessor->setValue('blind_print', $formatted['blind_print']);
		$templateProcessor->setValue('uv_print', $formatted['uv_print']);

		$templateProcessor->setValue('coverpaper_paper_type', $offer->coverpaper_paper_type);
		$templateProcessor->setValue('coverpaper_colors', $formatted['coverpaper_colors']);
		$templateProcessor->setValue('coverpaper_plastification', $formatted['coverpaper_plastification']);
		$templateProcessor->setValue('coverpaper_film_print', $formatted['coverpaper_film_print']);
		$templateProcessor->setValue('coverpaper_blind_print', $formatted['coverpaper_blind_print']);
		$templateProcessor->setValue('coverpaper_uv_print', $formatted['coverpaper_uv_print']);

		$templateProcessor->setValue('note', $this->getNote( $proposition, 'print' ));
		$templateProcessor->setValue('owner', $proposition->owner->name);
		$templateProcessor->setValue('date', date('d.m.Y.'));

		//$templateProcessor->saveAs(storage_path("offers/upit-$offer_id.docx"));//TODO: without saving

        $templateProcessor->saveAs("php://output");
        $contents = ob_get_contents();
        ob_end_clean();
        return response()->streamDownload(function () use ($contents) {
            echo $contents;
        }, "offers/upit-$offer_id.docx");

		return response()->download( storage_path( "offers/upit-$offer_id.docx" ) );

	}

	public function getPropDoc(BookProposition $proposition) {
		$templateProcessor = new TemplateProcessor(resource_path('proposition_template.docx'));
		$proposition->load(['owner']);
		$group = $proposition->bookCategories()->with('parent')->first();
		$book_type = $proposition->bookTypes()->with('parent')->first();
		//WARNING - STEF'S CODE :D
		//Blades: Proposition start, basic data
		$templateProcessor->setValue('project_name', $proposition->project_name); //duplicirano odozgo
		$templateProcessor->setValue('project_owner', $proposition->owner->name); //Propoziciju kreirao
		$templateProcessor->setValue('project_number', $proposition->project_number);
		$templateProcessor->setValue('project_number_additional', $proposition->additional_project_number);
		$templateProcessor->setValue('book_title', $proposition->title);
		$templateProcessor->setValue('author', $proposition->authors->implode('name', ', ')); //Popis autora
		$templateProcessor->setValue('concept', $proposition->concept);
		$templateProcessor->setValue('manuscript', $proposition->manuscript); //Dostavljen ili Nije dostavljen
		$templateProcessor->setValue('dotation', $proposition->dotation); //Da ili ne
		$templateProcessor->setValue('dotation_source', $proposition->dotation_origin); //"Nema podatka" ako nema
		$templateProcessor->setValue('dotation_amount', $proposition->dotation_amount); //"Nema podatka" ako nema
		$templateProcessor->setValue('project_extensions', implode(',', $proposition->possible_products)); //TODO popis dodatnih mogućnosti na kraju "osnovnih podataka"
		$templateProcessor->setValue('project_deadline', $proposition->deadline);
		$templateProcessor->setValue('project_importance', $proposition->priority);
		$templateProcessor->setValue('note_basic_data', $this->getNote($proposition, 'basic_data'));

		//Blades: categorization
		$templateProcessor->setValue('supergroup', $group->parent->name);
		$templateProcessor->setValue('upgroup', $group->parent->name);
		$templateProcessor->setValue('group', $group->name);
		$templateProcessor->setValue('book_type_basic_group', $book_type&&$book_type->parent?$book_type->parent->name:'');
		$templateProcessor->setValue('book_type_group', $book_type?$book_type->name:'');
		$templateProcessor->setValue('school_type', $proposition->schoolTypes->implode('name', ', '));
		$templateProcessor->setValue('reading_list', $proposition->school_assignment);
		$templateProcessor->setValue('primary_school', implode(',', $proposition->school_level));
		$templateProcessor->setValue('secondary_school', implode(',', $proposition->school_level));
		$templateProcessor->setValue('Bibliotheca', $proposition->bibliotecas()->first()?$proposition->bibliotecas()->first()->name:'');
		$templateProcessor->setValue('note_categorisation', $this->getNote($proposition, 'categorization'));
/*
		//Blades: marketing potential
		$templateProcessor->setValue('target_group', $proposition->owner->name);
		$templateProcessor->setValue('note_marketing_potential', $this->getNote($proposition, 'market_potential'));

		//Blades: technical data
		$templateProcessor->setValue('circulation', $proposition->circulations);
		$templateProcessor->setValue('additionals', $proposition->owner->name);
		$templateProcessor->setValue('pages_number', $proposition->owner->name);
		$templateProcessor->setValue('width', $proposition->owner->name);
		$templateProcessor->setValue('height', $proposition->owner->name);
		$templateProcessor->setValue('pages_binding', $proposition->owner->name);
		$templateProcessor->setValue('paper_type', $proposition->owner->name);
		$templateProcessor->setValue('colors', $proposition->owner->name);
		$templateProcessor->setValue('colors_first_pages', $proposition->owner->name);
		$templateProcessor->setValue('colors_last_pages', $proposition->owner->name);
		$templateProcessor->setValue('additional_request', $proposition->owner->name);
		$templateProcessor->setValue('cover_paper_type', $proposition->owner->name);
		$templateProcessor->setValue('cover_colors', $proposition->owner->name);
		$templateProcessor->setValue('plastification', $proposition->owner->name);
		$templateProcessor->setValue('film_print', $proposition->owner->name);
		$templateProcessor->setValue('blind_print', $proposition->owner->name);
		$templateProcessor->setValue('uv_plastification', $proposition->owner->name);
		$templateProcessor->setValue('note_technical_data', $this->getNote($proposition, 'technical_data'));

		//Blades: author expence; Koliko autora toliko zapisa - ovaj "count" samo stavio kao placeholder za brojku indentifikatora
		$templateProcessor->setValue('author_count', $proposition->authors);
		$templateProcessor->setValue('author_count_expense', $proposition->owner->name);
		$templateProcessor->setValue('author_count_precentage ', $proposition->owner->name);
		$templateProcessor->setValue('author_count_advance ', $proposition->owner->name);
		$templateProcessor->setValue('author_count_aditional', $proposition->owner->name);
		$templateProcessor->setValue('expense_name_count', $proposition->owner->name);
		$templateProcessor->setValue('expense_amount_count', $proposition->owner->name);

		//Blades: production expense
		$templateProcessor->setValue('text_price', $proposition->owner->name);
		$templateProcessor->setValue('advance_expence', $proposition->owner->name);
		$templateProcessor->setValue('precentage_netto_price', $proposition->owner->name);
		$templateProcessor->setValue('revision', $proposition->owner->name);
		$templateProcessor->setValue('lecture', $proposition->owner->name);
		$templateProcessor->setValue('correction', $proposition->owner->name);
		$templateProcessor->setValue('proofreading ', $proposition->owner->name);
		$templateProcessor->setValue('translation', $proposition->owner->name);
		$templateProcessor->setValue('index', $proposition->owner->name);
		$templateProcessor->setValue('epilogue', $proposition->owner->name);
		$templateProcessor->setValue('photo', $proposition->owner->name);
		$templateProcessor->setValue('illustration', $proposition->owner->name);
		$templateProcessor->setValue('technical_drawings', $proposition->owner->name);
		$templateProcessor->setValue('expert_revision', $proposition->owner->name);
		$templateProcessor->setValue('copyright', $proposition->owner->name);
		$templateProcessor->setValue('copyight_middleman', $proposition->owner->name);
		$templateProcessor->setValue('methodical_instrumentarium', $proposition->owner->name);
		$templateProcessor->setValue('selection', $proposition->owner->name);
		$templateProcessor->setValue('powerpoint', $proposition->owner->name);
		$templateProcessor->setValue('additional_expense_production', $proposition->owner->name);
		$templateProcessor->setValue('additional_expense_production_amount', $proposition->owner->name);
		$templateProcessor->setValue('note_production_expence', $this->getNote('production_expense'));

		//Blades: marketing expense
		$templateProcessor->setValue('marketing_expense', $proposition->owner->name);
		$templateProcessor->setValue('additional_expense_marketing', $proposition->owner->name);
		$templateProcessor->setValue('additional_expense_marketing_amount', $proposition->owner->name);
		$templateProcessor->setValue('note_marketing_expense', $this->getNote($proposition, 'marketing_expense'));

		//Blades: Layout and design expense
		$templateProcessor->setValue('layout_expense', $proposition->owner->name);
		$templateProcessor->setValue('note_layout_expense', $this->getNote($proposition, 'layout_note_budget'));
		$templateProcessor->setValue('design_expense ', $proposition->owner->name);
		$templateProcessor->setValue('note_design_expense ', $this->getNote($proposition, 'design_note_budget'));
*/

		//End of Stef's code :D

		//$templateProcessor->saveAs(storage_path("propositions/prop-{$proposition->id}.docx"));//TODO: without saving


        ob_start();
        $templateProcessor->saveAs("php://output");
        $contents = ob_get_contents();
        ob_end_clean();
        return response()->streamDownload(function () use ($contents) {
            echo $contents;
        }, "propositions/prop-{$proposition->id}.docx");

		return response()->download( storage_path( "propositions/prop-{$proposition->id}.docx" ) );

	}

	public function getWarehouse($id) {
		$proposition = BookProposition::find($id);
		$cover = $proposition->documents()->wherePivot( 'type', 'multimedia.jpg' )->first();
		if (!$cover) {
			return response()->json(['validated' => ['cover' => [ 'title' => 'Cover is missing', 'link' => '/proposition/'.$proposition->id.'/additionals/multimedia']]], 422);
		}

		return response()->json(['validated' => 'success']);
	}

	public function postWarehouse(Request $request, $id) {
		//set prop to archive
		$proposition = BookProposition::find($id);
		$cover = $proposition->documents()->wherePivot( 'type', 'multimedia.jpg' )->first();
		if (!$cover) {
			return response()->json(['validated' =>
				                         ['cover' => [ 'title' => 'Cover is missing', 'link' => url('/proposition/'.$proposition->id.'/additionals/multimedia')]
				                         ]
			], 422);
		}
		$proposition->status = 'archived';
		$proposition->completed_at = Carbon::now();
		$proposition->save();
		//create book
		$book = Book::create([
			'title' => $proposition->title,
			'description' => $proposition->concept,
			'proposition_id' => $proposition->id,
			'school_level' => json_encode($proposition->school_level),
			'school_assignment' => $proposition->school_assignment=='yes'?1:0,
			'retail_price' => $proposition->retail_price,
			'number_of_pages' => $proposition->number_of_pages,
			'width' => $proposition->width,
			'height' => $proposition->height,
			'paper_type' => $proposition->paper_type,
			'colors' => $proposition->colors,
			'colors_first_page' => $proposition->colors_first_page,
			'colors_last_page' => $proposition->colors_last_page,
			'cover_type' => $proposition->cover_type,
			'cover_paper_type' => $proposition->cover_paper_type,
			'cover_plastification' => $proposition->cover_plastification,
			'film_print' => $proposition->film_print=='yes'?1:0,
			'blind_print' => $proposition->blind_print=='yes'?1:0,
			'uv_print' => $proposition->uv_print=='yes'?1:0,
			'book_binding' => $proposition->book_binding,
			'cover' => $cover->link
		]);
		//TODO: sync relations
		return response()->json(['link' => $book->link]);
	}

	/**
	 * @param BookProposition $proposition
	 * @param string $step
	 * @param string $status
	 *
	 */
	public function saveStepStatus(BookProposition $proposition, $step, $status = 'saved') {
		if (is_array( $proposition->step_status )) {
			$out = $proposition->step_status;
			$out[$step] = $status;
			$proposition->step_status = $out;
		}
		else {
			$proposition->step_status = [
				$step => $status
			];
		}
		$proposition->save();
	}
}