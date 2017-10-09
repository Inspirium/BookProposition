<?php

namespace Inspirium\BookProposition\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Notifications\TaskAssigned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Inspirium\BookManagement\Models\Book;
use Inspirium\BookProposition\Models\AuthorExpense;
use Inspirium\BookProposition\Models\BookProposition;
use Inspirium\BookProposition\Models\PropositionNote;
use Inspirium\BookProposition\Models\PropositionOption;
use Inspirium\FileManagement\Models\File;
use Inspirium\HumanResources\Models\Employee;
use Inspirium\TaskManagement\Models\Task;

class PropositionController extends Controller {

	public function deleteProposition($id) {
		BookProposition::destroy($id);
		return response()->json([]);
	}

	public function restoreProposition($id) {
		$proposition = BookProposition::withTrashed()->find($id);
		$proposition->restore();
		return response()->json([]);
	}

	public function assignProposition(Request $request, $id) {
		$proposition = BookProposition::find($id);
		$departments = $request->input('departments');
		$employees = $request->input('employees');
		$assigner = Employee::where('user_id', Auth::id())->first();
		if ($employees) {
			$employees = array_pluck($employees, 'id');
				$task = new Task();
				$task->assigner()->associate($assigner);
				$task->name = 'Proposition: ' . $proposition->title;
				$task->related()->associate($proposition);
				$task->description = 'You have been assigned to edit the following proposition';
				$task->status = 'new';
				$task->type = 1;
				$task->save();
				$task->employees()->attach($employees);
				foreach ($employees as $employee_id) {
					$employee = Employee::find($employee_id);
					$employee->user->notify(new TaskAssigned( $task ));
				}
		}
		else if ($departments) {
			$departments = array_pluck($departments, 'id');
			$task = new Task();
			$task->assigner()->associate($assigner);
			$task->name = 'Proposition: ' . $proposition->title;
			$task->related()->associate($proposition);
			$task->description = 'You have been assigned to edit the following proposition';
			$task->status = 'new';
			$task->type = 1;
			$task->save();
			$task->departments()->attach($departments);
			//TODO: send notification
		}
	}

	public function getInitData($id) {
		$proposition = BookProposition::withTrashed()->find($id);
		$out = [];
		$out['id'] = $proposition->id;
		$out['created_at'] = $proposition->created_at;
		$out['updated_at'] = $proposition->updated_at;
		$out['deleted_at'] = $proposition->deleted_at;
		$out['owner'] = $proposition->owner;
		return response()->json($out);
	}

	public function initProposition(Request $request) {
		$proposition = new BookProposition();
		$proposition->project_name = $request->input('project_name');
		$proposition->project_number = $request->input('project_number');
		$proposition->additional_project_number = $request->input('additional_project_number');
		$proposition->save();
		return response()->json(['id' => $proposition->id]);
 	}

	public function getPropositionStep($id, $step) {
		$proposition = BookProposition::withTrashed()->find($id);
		$allowed_steps = [
			'basic_data', 'translation', 'start', 'categorization', 'market_potential', 'technical_data', 'print',
			'authors_expenses', 'production_expense', 'marketing_expense', 'distribution_expense', 'layout_expense',
			'deadline', 'compare'
		];
		$out = [];
		if (in_array($step, $allowed_steps)) {
			$function = 'get' . str_replace(' ', '', ucfirst(str_replace('_', ' ',$step)));
			$out = $this->$function($proposition);
		}

		$out['id'] = $proposition->id;
		$out['created_at'] = $proposition->created_at;
		$out['updated_at'] = $proposition->updated_at;
		$out['deleted_at'] = $proposition->deleted_at;
		$out['owner'] = $proposition->owner;

		return response()->json($out);
	}

	public function setPropositionStep(Request $request, $id, $step) {
		$proposition = BookProposition::withTrashed()->find($id);
		$allowed_steps = [
			'basic_data', 'translation', 'start', 'categorization', 'market_potential', 'technical_data', 'print',
			'authors_expenses', 'production_expense', 'marketing_expense', 'distribution_expense', 'layout_expense',
			'deadline', 'compare'
		];
		$out = [];
		if (in_array($step, $allowed_steps)) {
			$function = 'set' . str_replace(' ', '', ucfirst(str_replace('_', ' ',$step)));
			$out = $this->$function($request, $proposition);
		}
		$out['id'] = $proposition->id;

		return response()->json($out);
	}

	//TODO: move

	private function getStart(BookProposition $proposition) {
		return [
			'project_number' => $proposition->project_number,
			'project_name' => $proposition->project_name,
			'additional_project_number' => $proposition->additional_project_number,
			'note' => $this->getNote($proposition, 'start')
		];
	}

	private function setStart(Request $request, BookProposition $proposition) {
		$proposition->project_number = $request->input('project_number');
		$proposition->project_name = $request->input('project_name');
		$proposition->additional_project_number = $request->input('additional_project_number');
		$this->setNote($proposition, $request->input('note'), 'start');
		$proposition->save();
	}

	private function setNote(BookProposition $proposition, $text, $type) {
		$note = $proposition->notes()->where('type', '=', $type)->first();
		if (!$note) {
			$note = new PropositionNote(['type' => $type, 'proposition_id' => $proposition->id]);
		}
		$note->note = $text;
		$note->save();
	}

	private function getNote(BookProposition $proposition, $type) {
		$note = $proposition->notes()->where('type', '=', $type)->first();
		if ($note) {
			return $note->note;
		}
		return '';
	}

	/**
	 * @param BookProposition $proposition
	 *
	 * @return array
	 */
	private function getBasicData(BookProposition $proposition) {
		return [
			'title' => $proposition->title,
			'authors' => $proposition->authors()->get(),
			'concept' => $proposition->concept,
			'possible_products' => $proposition->possible_products,
			'dotation' => $proposition->dotation,
			'dotation_amount' => $proposition->dotation_amount,
			'dotation_origin' => $proposition->dotation_origin,
			'manuscript' => $proposition->manuscript,
			'manuscript_documents' => $proposition->documents()->wherePivot('type', 'manuscript')->get(),
			'note' => $this->getNote($proposition, 'basic_data')
		];
	}

	private function setBasicData(Request $request, BookProposition $proposition) {
		$proposition->title = $request->input('title');
		$proposition->concept = $request->input('concept');
		$proposition->possible_products = $request->input('possible_products');
		$proposition->dotation = $request->input('dotation');
		$proposition->dotation_amount = $request->input('dotation_amount');
		$proposition->dotation_origin = $request->input('dotation_origin');
		$proposition->manuscript = $request->input('manuscript');

		foreach ($request->input('manuscript_documents') as $document) {
			$file = File::find($document['id']);
			$file->title = $document['title'];
			$file->save();
			if (!$proposition->documents()->wherePivot('type', 'manuscript')->get()->contains($document['id'])) {
				$proposition->documents()->save( $file, [ 'type' => 'manuscript' ] );
			}
		}
		$authors = [];
		foreach ($request->input('authors') as $author) {
			$authors[] = $author['id'];
		}
		$proposition->authors()->sync($authors);
		$proposition->save();
		$this->setNote($proposition, $request->input('note'), 'basic_data');
	}

	private function getCategorization(BookProposition $proposition) {
		return [
			'group' => $proposition->bookCategories()->with('parent')->first(),
			'book_type' => $proposition->bookTypes()->first(),
			'school_type' => $proposition->schoolTypes,
			'school_level' => $proposition->school_level,
			'school_assignment' => $proposition->school_assignment,
			'school_subject' => $proposition->schoolSubjects()->first(),
			'biblioteca' => $proposition->bibliotecas()->first(),
			'note' => $this->getNote($proposition, 'categorization')
		];
	}

	private function setCategorization(Request $request, BookProposition $proposition) {
		$proposition->bookCategories()->sync($request->input('group'));
		$proposition->bookTypes()->sync($request->input('book_type'));
		$proposition->schoolTypes()->sync($request->input('school_type'));
		$proposition->schoolSubjects()->sync($request->input('school_subject_detailed'));
		$proposition->school_level = $request->input('school_level');
		$proposition->school_assignment = $request->input('school_assignment');
		$proposition->bibliotecas()->sync($request->input('biblioteca'));
		$proposition->save();
		$this->setNote($proposition, $request->input('note'), 'categorization');
	}

	private function getMarketPotential(BookProposition $proposition) {
		return [
			'main_target' => $proposition->main_target,
			'market_potential_documents' => $proposition->documents()->wherePivot('type', 'market_potential')->get(),
			'note' => $this->getNote($proposition, 'market_potential')
		];
	}

	public function setMarketPotential(Request $request, BookProposition $proposition) {
		$proposition->main_target = $request->input('main_target');
		foreach ($request->input('market_potential_documents') as $document) {
			$file = File::find($document['id']);
			$file->title = $document['title'];
			$file->save();
			if (!$proposition->documents()->wherePivot('type', 'market_potential')->get()->contains($document['id'])) {
				$proposition->documents()->save( $file, [ 'type' => 'market_potential' ] );
			}
		}
		$this->setNote($proposition, $request->input('note'), 'market_potential');
	}

	private function getTechnicalData(BookProposition $proposition) {
		return [
			'additions' => $proposition->additions,
			'circulations' => $proposition->circulations,
			'number_of_pages' => $proposition->number_of_pages,
			'width' => $proposition->width,
			'height' => $proposition->height,
			'paper_type' => $proposition->paper_type,
			'book_binding' => $proposition->book_binding,
			'additional_work' => $proposition->additional_work,
			'colors' => $proposition->colors,
			'colors_first_page' => $proposition->colors_first_page,
			'colors_last_page' => $proposition->colors_last_page,
			'cover_type' => $proposition->cover_type,
			'cover_paper_type' => $proposition->cover_paper_type,
			'cover_colors' => $proposition->cover_colors,
			'cover_plastification' => $proposition->cover_plastification,
			'film_print' => $proposition->film_print,
			'blind_print' => $proposition->blind_print,
			'uv_print' => $proposition->uv_print,
			'note' => $this->getNote($proposition, 'technical_data')
		];
	}

	public function setTechnicalData(Request $request, BookProposition $proposition) {
		$proposition->number_of_pages = $request->input('number_of_pages');
		$proposition->width = $request->input('width');
		$proposition->height = $request->input('height');
		$proposition->paper_type = $request->input('paper_type');
		$proposition->additional_work = $request->input('additional_work');
		$proposition->colors = $request->input('colors');
		$proposition->colors_first_page = $request->input('colors_first_page');
		$proposition->colors_last_page = $request->input('colors_last_page');
		$proposition->cover_type = $request->input('cover_type');
		$proposition->cover_paper_type = $request->input('cover_paper_type');
		$proposition->cover_colors = $request->input('cover_colors');
		$proposition->cover_plastification = $request->input('cover_plastification');
		$proposition->film_print = $request->input('film_print');
		$proposition->blind_print = $request->input('blind_print');
		$proposition->uv_print = $request->input('uv_print');
		$proposition->additions = $request->input('additions');
		//$proposition->circulations = $request->input('circulations');
		$proposition->book_binding = $request->input('book_binding');
		$circs = [];
		foreach ($request->input('circulations') as $circulation) {
			$option = PropositionOption::find( $circulation['id'] );
			if ($option) {
				//do not modify existing;
				$circs[] = $option->id;
				continue;
			}
			$option = new PropositionOption();
			$option->title = $circulation['title'];
			//$option->proposition_id = $id;
			$option->cover_type = $request->input('cover_type');
			$option->cover_paper_type = $request->input('cover_paper_type');
			$option->cover_colors = $request->input('cover_colors');
			$option->cover_plastification = $request->input('cover_plastification');
			$option->film_print = $request->input('film_print');
			$option->uv_print = $request->input('uv_print');
			$option->blind_print = $request->input('blind_print');
			$option->colors = $request->input('colors');
			$option->paper_type = $request->input('paper_type');
			$option->hard_cover_circulation = $request->input('hard_cover_circulation');
			$option->soft_cover_circulation = $request->input('soft_cover_circulation');
			$option->book_binding = $request->input('book_binding');
			$option->colors_first_page = $request->input('colors_first_page');
			$option->colors_last_page = $request->input('color_last_page');
			$option->number_of_pages = $request->input('number_of_pages');
			$option->calculated_profit_percent = 18;
			$option->shop_percent = 20;
			$option->vat_percent = 5;
			$option->save();
			$proposition->options()->save($option);
			$circs[] = $option->id;
		}
		/** @var PropositionOption $option */
		$out = [];
		foreach ($proposition->options as $option) {
			if (!in_array($option->id, $circs)) {
				$option->delete();
			}
			else {
				$out[] =  ['id' => $option->id, 'title' => $option->title];
			}
		}
		$this->setNote($proposition, $request->input('note'), 'technical_data');
		return ['circulations' => $out];
	}

	private function getPrint(BookProposition $proposition) {
		return [
			'offers' => $proposition->offers,
			'note' => $this->getNote($proposition, 'print')
		];
	}

	public function setPrint(Request $request, BookProposition $proposition) {
		$circulations = [];
		foreach ($request->input('offers') as $offer_id => $offer) {
			$option = PropositionOption::find( $offer_id );
			if (!$option) {
				continue;
			}
			$option->mapModel($offer);
			$option->save();
			$circulations[] = ['title' => $option->title, 'id' => $option->id];
		}
		$this->setNote($proposition, $request->input('note'), 'print');
	}

	private function getAuthorsExpense(BookProposition $proposition) {
		return [
			'expenses' => $proposition->author_expenses,
			'other' => $proposition->author_other_expense,
			'note' => $this->getNote($proposition, 'authors_expense')
		];
	}

	public function setAuthorsExpense(Request $request, BookProposition $proposition) {
		foreach($request->input('expenses') as $author_id => $expense) {

			if (isset($expense['id']) && $expense['id']) { //we have id, so that means it was loaded from db, just update it
				$e = AuthorExpense::find($expense['id']);
				$e->fill($expense);
			}
			else {
				$e = AuthorExpense::create($expense);
				$e->author_id = $author_id;
				$e->proposition_id = $id;
			}
			$e->save();
		}
		$proposition->author_other_expense = $request->input('other');
		$this->setNote($proposition, $request->input('note'), 'authors_expense');
	}

	private function getProductionExpense(BookProposition $proposition) {
		return [
			'text_price' => $proposition->text_price,
			'text_price_amount' => $proposition->text_price_amount,
			'accontation' => $proposition->accontation,
			'netto_price_percentage' => $proposition->netto_price_percentage,
			'reviews' => $proposition->reviews,
			'lecture' => $proposition->lecture,
			'lecture_amount' => $proposition->lecture_amount,
			'correction' => $proposition->correction,
			'correction_amount' => $proposition->correction_amount,
			'proofreading' => $proposition->proofreading,
			'proofreading_amount' => $proposition->proofreading_amount,
			'translation' => $proposition->translation,
			'translation_amount' => $proposition->translation_amount,
			'index' => $proposition->index,
			'index_amount' => $proposition->index_amount,
			'epilogue' => $proposition->epilogue,
			'photos' => $proposition->photos,
			'photos_amount' => $proposition->photos_amount,
			'illustrations' => $proposition->illustrations,
			'illustrations_amount' => $proposition->illustrations_amount,
			'technical_drawings' => $proposition->technical_drawings,
			'technical_drawings_amount' => $proposition->technical_drawings_amount,
			'expert_report' => $proposition->expert_report,
			'copyright' => $proposition->copyright,
			'copyright_mediator' => $proposition->copyright_mediator,
			'selection' => $proposition->selection,
			'powerpoint_presentation' => $proposition->powerpoint_presentation,
			'methodical_instrumentarium' => $proposition->methodical_instrumentarium,
			'additional_expense' => $proposition->production_additional_expense,
			'note' => $this->getNote($proposition, 'production_expense')
		];
	}

	public function setProductionExpense(Request $request, BookProposition $proposition) {
		$proposition->text_price = $request->input('text_price');
		$proposition->text_price_amount = $request->input('text_price_amount');
		$proposition->accontation = $request->input('accontation');
		$proposition->netto_price_percentage = $request->input('netto_price_percentage');
		$proposition->reviews = $request->input('reviews');
		$proposition->lecture = $request->input('lecture');
		$proposition->lecture_amount = $request->input('lecture_amount');
		$proposition->correction = $request->input('correction');
		$proposition->correction_amount = $request->input('correction_amount');
		$proposition->proofreading = $request->input('proofreading');
		$proposition->proofreading_amount = $request->input('proofreading_amount');
		$proposition->translation = $request->input('translation');
		$proposition->translation_amount = $request->input('translation_amount');
		$proposition->index = $request->input('index');
		$proposition->index_amount = $request->input('index_amount');
		$proposition->epilogue = $request->input('epilogue');
		$proposition->photos = $request->input('photos');
		$proposition->photos_amount = $request->input('photos_amount');
		$proposition->illustrations = $request->input('illustrations');
		$proposition->illustrations_amount = $request->input('illustrations_amount');
		$proposition->technical_drawings = $request->input('technical_drawings');
		$proposition->technical_drawings_amount = $request->input('technical_drawings_amount');
		$proposition->expert_report = $request->input('expert_report');
		$proposition->copyright = $request->input('copyright');
		$proposition->copyright_mediator = $request->input('copyright_mediator');
		$proposition->selection = $request->input('selection');
		$proposition->powerpoint_presentation = $request->input('powerpoint_presentation');
		$proposition->methodical_instrumentarium = $request->input('methodical_instrumentarium');
		$proposition->production_additional_expense = $request->input('additional_expense');
		$this->setNote($proposition, $request->input('note'), 'production_expense');
	}

	private function getMarketingExpense(BookProposition $proposition) {
		return [
			'expense' => $proposition->marketing_expense,
			'additional_expense' => $proposition->marketing_additional_expense,
			'note' => $this->getNote($proposition, 'marketing_expense')
		];
	}

	public function setMarketingExpense(Request $request, BookProposition $proposition) {
		$proposition->marketing_expense = $request->input('expense');
		$proposition->marketing_additional_expense = $request->input('additional_expense');
		$this->setNote($proposition, $request->input('note'), 'marketing_expense');
	}

	private function getDistributionExpense(BookProposition $proposition) {
		return [
			'margin' => $proposition->margin,
			'note' => $this->getNote($proposition, 'distribution_expense')
		];
	}

	public function setDistributionExpense(Request $request, BookProposition $proposition) {
		$proposition->margin = $request->input('margin');
		$this->setNote($proposition, $request->input('note'), 'distribution_expense');
	}

	private function getLayoutExpense(BookProposition $proposition) {
		return [
			'layout_complexity' => $proposition->layout_complexity,
			'layout_include' => $proposition->layout_include,
			'layout_note' => $proposition->layout_note,
			'design_complexity' => $proposition->design_complexity,
			'design_include' => $proposition->design_include,
			'design_note' => $proposition->design_note,
			'note' => $this->getNote($proposition, 'layout_expense')
		];
	}

	public function setLayoutExpense(Request $request, BookProposition $proposition) {
		$proposition->layout_complexity = $request->input('layout_complexity');
		$proposition->layout_include = $request->input('layout_include');
		$proposition->design_complexity = $request->input('design_complexity');
		$proposition->design_include = $request->input('design_include');
		$proposition->layout_note = $request->input('layout_note');
		$proposition->design_note = $request->input('design_note');
		$this->setNote($proposition, $request->input('note'), 'layout_expense');
	}

	private function getDeadline(BookProposition $proposition) {
		return [
			'date' => $proposition->deadline,
			'priority' => $proposition->priority,
			'note' => $this->getNote($proposition, 'deadline')
		];
	}

	public function setDealine(Request $request, BookProposition $proposition) {
		$proposition->deadline = $request->input('date');
		$proposition->priority = $request->input('priority');
		$this->setNote($proposition, $request->input('note'), 'deadline');
	}

	private function getCompare(BookProposition $proposition) {
		return $proposition->expenses;
	}

	private function setCompare(Request $request, $proposition) {
		$proposition->expenses = $request->input('expenses');
		$proposition->save();
	}

	public function getFiles($id, $type) {
		$proposition = BookProposition::withTrashed()->find($id);
		return [
			'files' => $proposition->documents()->wherePivot('type', $type)->wherePivot('final', false)->get(),
			'final' => $proposition->documents()->wherePivot('type', $type)->wherePivot('final', true)->get()
		];
	}

	public function setFiles(Request $request, $id, $type) {
		$proposition = BookProposition::withTrashed()->find($id);
		foreach ($request->input('initial') as $document) {
			$file = File::find($document['id']);
			$file->title = $document['title'];
			$file->save();
			if (!$proposition->documents()->wherePivot('type', $type)->get()->contains($document['id'])) {
				$proposition->documents()->save( $file, [ 'type' => $type ] );
			}
		}
		foreach ($request->input('final') as $document) {
			$file = File::find($document['id']);
			$file->title = $document['title'];
			$file->save();
			if (!$proposition->documents()->wherePivot('type', $type)->get()->contains($document['id'])) {
				$proposition->documents()->save( $file, [ 'type' => $type, 'final' => true ] );
			}
		}
	}
}