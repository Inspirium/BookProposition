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
		$proposition   = BookProposition::withTrashed()->find( $id );
		if (!$proposition) {
			return response()->json([]);
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

		$out['proposition_id']         = $proposition->id;
		$out['created_at'] = $proposition->created_at;
		$out['updated_at'] = $proposition->updated_at;
		$out['deleted_at'] = $proposition->deleted_at;
		$out['owner']      = $proposition->owner;
		$out['type']      = $type;

		return response()->json( $out );
	}

	public function setPropositionStep( Request $request, $id, $step, $type = null ) {
		$proposition   = BookProposition::withTrashed()->find( $id );
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
		$proposition->status                    = 'unfinished';
		$employee = Auth::user();
		$proposition->owner()->associate($employee);
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
		$proposition->manuscript        = $request->input( 'manuscript' );

		foreach ( $request->input( 'manuscript_documents' ) as $document ) {
			$file        = File::find( $document['id'] );
			$file->title = $document['title'];
			$file->save();
			if ( ! $proposition->documents()->wherePivot( 'type', 'manuscript' )->get()->contains( $document['id'] ) ) {
				$proposition->documents()->save( $file, [ 'type' => 'manuscript' ] );
			}
		}
		$authors  = [];
		$expenses = [];
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
	}

	private function getCategorization( BookProposition $proposition ) {
		return [
			'group'             => $proposition->bookCategories()->with( 'parent' )->first(),
			'book_type'         => $proposition->bookTypes()->first(),
			'school_type'       => $proposition->schoolTypes,
			'school_level'      => $proposition->school_level,
			'school_assignment' => $proposition->school_assignment,
			'school_subject'    => $proposition->schoolSubjects()->first(),
			'biblioteca'        => $proposition->bibliotecas()->first(),
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
		$proposition->bibliotecas()->sync( $request->input( 'biblioteca' ) );
		$proposition->save();
		$this->setNote( $proposition, $request->input( 'note' ), 'categorization' );
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
		$proposition->film_print           = $request->input( 'film_print' );
		$proposition->blind_print          = $request->input( 'blind_print' );
		$proposition->uv_print             = $request->input( 'uv_print' );
		$proposition->additions            = $request->input( 'additions' );
		//$proposition->circulations = $request->input('circulations');
		$proposition->book_binding = $request->input( 'book_binding' );
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
		return [
			'offers' => $proposition->offers,
			'note'   => $this->getNote( $proposition, 'print' )
		];
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
			'note'    => $this->getNote( $proposition, 'authors_expense' ),

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
			foreach ( $proposition->authorOtherExpenses as $o ) {
				if ( ! in_array( $o->id, $other_expenses ) ) {
					$o->delete();
				}
			}
		}
		$proposition->save();
		$this->setNote( $proposition, $request->input( 'note' ), 'authors_expense' );

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
			'group'                     => $proposition->bookCategories()->with( 'parent.parent' )->first(),
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
		$this->setNote( $proposition, $request->input( 'note' ), 'layout_note_' . $type );
		$this->setNote( $proposition, $request->input( 'note' ), 'design_note_' . $type );
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
			'offers' => $proposition->options->mapWithKeys(function($option) {
				return [$option['id'] => $option['title']];
			}),
			'selected_circulation' => $final?$final->id:0
		];
	}

	private function setPriceDefinition(Request $request, BookProposition $proposition) {
		$proposition->price_first_year = $request->input('price_first_year');
		$proposition->price_second_year = $request->input('price_second_year');
		$proposition->retail_price = $request->input('retail_price');
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


	}

	private function setDeadline( Request $request, BookProposition $proposition ) {
		$proposition->deadline = $request->input( 'date' );
		$proposition->priority = $request->input( 'priority' );
		$this->setNote( $proposition, $request->input( 'note' ), 'deadline' );
		$proposition->save();
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
	}

	private function getCompare( BookProposition $proposition ) {
		$marketing_expense = $proposition->marketingExpenses->keyBy('type');
		$production_expense = $proposition->productionExpenses->keyBy('type');
		$authors = $proposition->authors()->with('expenses')->get();
		$requests = $proposition->approvalRequests()->orderBy('updated_at', 'desc')->get()->groupBy('designation');
		return [
			'marketing_expense' => $marketing_expense,
			'production_expense' => $production_expense,
			'requests' => $requests,
			'authors' => $authors
		];
	}

	private function setCompare( Request $request, $proposition ) {

	}

	public function getFiles( $id, $type ) {
		$proposition = BookProposition::withTrashed()->find( $id );

		return [
			'files' => $proposition->documents()->wherePivot( 'type', $type )->wherePivot( 'final', false )->get(),
			'final' => $proposition->documents()->wherePivot( 'type', $type )->wherePivot( 'final', true )->get()
		];
	}

	public function setFiles( Request $request, $id, $type ) {
		$proposition = BookProposition::withTrashed()->find( $id );
		foreach ( $request->input( 'initial' ) as $document ) {
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
	}

	public function getMultimedia($id) {
		$proposition = BookProposition::withTrashed()->find( $id );

		return [
			'webshop' => $this->getNote($proposition, 'webshop'),
			'jpg' => $proposition->documents()->wherePivot( 'type', 'multimedia.jpg' )->get(),
			'psd' => $proposition->documents()->wherePivot( 'type', 'multimedia.psd' )->get()
		];
	}

	public function setMultimedia(Request $request, $id) {
		$proposition = BookProposition::withTrashed()->find( $id );
		$this->setNote($proposition, $request->input('webshop'), 'webshop');
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
					'type'  => 'multimedia.psd'
				] );
			}
		}
	}

	public function getMarketing($id) {
		$proposition = BookProposition::withTrashed()->find( $id );

		return [
			'cover' => $proposition->documents()->wherePivot( 'type', 'marketing.cover' )->get(),
			'leaflet' => $proposition->documents()->wherePivot( 'type', 'marketing.leaflet' )->get()
		];
	}

	public function setMarketing(Request $request, $id) {
		$proposition = BookProposition::withTrashed()->find( $id );
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
			if ( ! $proposition->documents()->wherePivot( 'type', 'marketing.leaflet')->get()->contains( $document['id'] ) ) {
				$proposition->documents()->save( $file, [
					'type'  => 'marketing.leaflet'
				] );
			}
		}
	}

	public function getOfferDoc($id, $offer_id, $doc_type) {
		$proposition = BookProposition::find($id);
		$offer = PropositionOption::find($offer_id);
		$templateProcessor = new TemplateProcessor(resource_path('print_offer_template.docx'));
		$templateProcessor->setValue('project_name', $proposition->title);
		$templateProcessor->setValue('circulation', $offer->title);
		$templateProcessor->setValue('paper_type', $offer->paper_type);
		$templateProcessor->setValue('book_binding', $offer->book_binding);
		$templateProcessor->setValue('colors', $offer->colors);
		$templateProcessor->setValue('colors_first_page', $offer->colors_first_page);
		$templateProcessor->setValue('colors_last_page', $offer->colors_last_page);
		$templateProcessor->setValue('additional_work', $offer->additional_work);
		$templateProcessor->setValue('cover_type', $offer->cover_type);
		$templateProcessor->setValue('hard_cover_circulation', $offer->hard_cover_circulation);
		$templateProcessor->setValue('soft_cover_circulation', $offer->soft_cover_circulation);
		$templateProcessor->setValue('cover_paper_type', $offer->cover_paper_type);
		$templateProcessor->setValue('cover_colors', $offer->cover_colors);
		$templateProcessor->setValue('cover_plastification', $offer->cover_plastification);
		$templateProcessor->setValue('film_print', $offer->film_print);
		$templateProcessor->setValue('blind_print', $offer->blind_print);
		$templateProcessor->setValue('uv_print', $offer->uv_print);
		$templateProcessor->setValue('note', $this->getNote( $proposition, 'print' )); //TODO: fix for proper note
		$templateProcessor->setValue('owner', $proposition->owner->name);
		$templateProcessor->setValue('date', date('d.m.Y.'));

		$templateProcessor->saveAs(storage_path("offers/upit-$offer_id.docx"));//TODO: without saving

		if ($doc_type === 'pdf') {
			$unoconv = Unoconv::create(array(
				'timeout'          => 42,
				'unoconv.binaries' => '/usr/bin/unoconv',
			));
			$unoconv->transcode(storage_path( "offers/upit-$offer_id.docx" ), 'pdf', storage_path( "offers/upit-$offer_id.pdf" ));
			return response()->download( storage_path( "offers/upit-$offer_id.pdf" ) );;
		}
		else {
			return response()->download( storage_path( "offers/upit-$offer_id.docx" ) );
		}
	}

	public function postWarehouse(Request $request, $id) {
		//set prop to archive
		$proposition = BookProposition::find($id);
		$proposition->status = 'archived';
		$proposition->completed_at = Carbon::now();
		$proposition->save();
		//create book
		$book = Book::create([
			'title' => $proposition->title,
			'description' => $proposition->concept,
			'proposition_id' => $proposition->id,
			'school_level' => $proposition->school_level,
			'school_assignment' => $proposition->school_assignment,
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
			'film_print' => $proposition->film_print,
			'blind_print' => $proposition->blind_print,
			'uv_print' => $proposition->uv_print,
			'book_binding' => $proposition->book_binding,
			'cover' => $proposition->documents()->wherePivot( 'type', 'multimedia.jpg' )->first()->link
		]);
		//TODO: sync relations
		return response()->json(['link' => $book->link]);
	}
}