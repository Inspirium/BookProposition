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
use Inspirium\BookProposition\Models\PropositionOption;
use Inspirium\FileManagement\Models\File;
use Inspirium\HumanResources\Models\Employee;
use Inspirium\TaskManagement\Models\Task;

class PropositionController extends Controller {

	public function getProposition( $id ) {
		$out = $this->buildResponse($id);
		return response()->json($out);
	}

	public function saveProposition( Request $request, $id = null ) {
		/** @var BookProposition $proposition */
		$proposition = BookProposition::firstOrCreate(['id' => $id]);
		if (!$proposition->owner_id) {
			$employee = Employee::where('user_id', Auth::id())->first();
			$proposition->owner()->associate($employee);
		}
		switch ($request->input('step')) {
			case 'basic_data':
				$proposition->title = $request->input('data.title');
				$proposition->concept = $request->input('data.concept');
				$proposition->possible_products = $request->input('data.possible_products');
				$proposition->dotation = $request->input('data.dotation');
				$proposition->dotation_amount = $request->input('data.dotation_amount');
				$proposition->dotation_origin = $request->input('data.dotation_origin');
				$proposition->manuscript = $request->input('data.manuscript');
				foreach ($request->input('data.manuscript_documents') as $document) {

					$file = File::find($document['id']);
					$file->title = $document['title'];
					$file->save();
					if (!$proposition->documents->contains($document['id'])) {
						$proposition->documents()->save( $file, [ 'type' => 'manuscript' ] );
					}
				}
				$authors = [];
				foreach ($request->input('data.authors') as $author) {
					$authors[] = $author['id'];
				}
				$proposition->authors()->sync($authors);
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
				$proposition->biblioteca_id = $request->input('data.biblioteca');
				break;
			case 'market_potential':
				$proposition->main_target = $request->input('data.main_target');
				foreach ($request->input('data.market_potential_documents') as $document) {
					$file = File::find($document['id']);
					$file->title = $document['title'];
					$file->save();
					if (!$proposition->documents->contains($document['id'])) {
						$proposition->documents()->save( $file, [ 'type' => 'market_potential' ] );
					}
				}
				break;
			case 'technical_data':
				$proposition->number_of_pages = $request->input('data.number_of_pages');
				$proposition->width = $request->input('data.width');
				$proposition->height = $request->input('data.height');
				$proposition->paper_type = $request->input('data.paper_type');
				$proposition->additional_work = $request->input('data.additional_work');
				$proposition->colors = $request->input('data.colors');
				$proposition->colors_first_page = $request->input('data.colors_first_page');
				$proposition->colors_last_page = $request->input('data.colors_last_page');
				$proposition->cover_type = $request->input('data.cover_type');
				$proposition->cover_paper_type = $request->input('data.cover_paper_type');
				$proposition->cover_colors = $request->input('data.cover_colors');
				$proposition->cover_plastification = $request->input('data.cover_plastification');
				$proposition->film_print = $request->input('data.film_print');
				$proposition->blind_print = $request->input('data.blind_print');
				$proposition->uv_print = $request->input('data.uv_print');
				$proposition->additions = $request->input('data.additions');
				//$proposition->circulations = $request->input('data.circulations');
				$proposition->book_binding = $request->input('data.book_binding');
				$circs = [];
				foreach ($request->input('data.circulations') as $circulation) {
					$option = PropositionOption::find( $circulation['id'] );
					if ($option) {
						//do not modify existing;
						$circs[] = $option->id;
						continue;
					}
					$option = new PropositionOption();
					$option->title = $circulation['title'];
					//$option->proposition_id = $id;
					$option->cover_type = $request->input('data.cover_type');
					$option->cover_paper_type = $request->input('data.cover_paper_type');
					$option->cover_colors = $request->input('data.cover_colors');
					$option->cover_plastification = $request->input('data.cover_plastification');
					$option->film_print = $request->input('data.film_print');
					$option->uv_print = $request->input('data.uv_print');
					$option->blind_print = $request->input('data.blind_print');
					$option->colors = $request->input('data.colors');
					$option->paper_type = $request->input('data.paper_type');
					$option->hard_cover_circulation = $request->input('data.hard_cover_circulation');
					$option->soft_cover_circulation = $request->input('data.soft_cover_circulation');
					$option->book_binding = $request->input('data.book_binding');
					$option->colors_first_page = $request->input('data.colors_first_page');
					$option->colors_last_page = $request->input('data.color_last_page');
					$option->number_of_pages = $request->input('data.number_of_pages');
					$option->calculated_profit_percent = 18;
					$option->shop_percent = 20;
					$option->vat_percent = 5;
					$option->save();
					$proposition->options()->save($option);
					$circs[] = $option->id;
				}
				/** @var PropositionOption $option */
				foreach ($proposition->options as $option) {
					if (!in_array($option->id, $circs)) {
						$option->delete();
					}
				}
				break;
			case 'print':
				$circulations = [];
				foreach ($request->input('data.offers') as $offer_id => $offer) {
					$option = PropositionOption::find( $offer_id );
					if (!$option) {
						continue;
					}
					$option->mapModel($offer);
					$option->save();
					$circulations[] = ['title' => $option->title, 'id' => $option->id];
				}
				//$proposition->circulations = $circulations;
				break;
			case 'authors_expense':
				foreach($request->input('data.expenses') as $author_id => $expense) {

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
				$proposition->author_other_expense = $request->input('data.other');
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
				$proposition->production_additional_expense = $request->input('data.additional_expense');
				break;
			case 'marketing_expense':
				$proposition->marketing_expense = $request->input('data.expense');
				$proposition->marketing_additional_expense = $request->input('data.additional_expense');
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
		}
		$proposition->status = 'unfinished';
		$proposition->save();
		$out = $this->buildResponse($proposition->id);
		return response()->json($out);
	}

	/**
	 * @param int $proposition
	 *
	 * @return array
	 */
	private function buildResponse($proposition) {
		//TODO: build proposition object according to access rights
		$proposition = BookProposition::withTrashed()->find($proposition);
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
				'manuscript_documents' => $proposition->documents()->wherePivot('type', 'manuscript')->get(),
				'authors' => $proposition->authors,
			],
			'categorization' => [
				'supergroup' => $proposition->supergroup_id,
				'supergroup_text' => $proposition->supergroup_id?$proposition->supergroup->name:'',
				'upgroup' => $proposition->upgroup_id,
				'upgroup_coef' => $proposition->upgroup_id?$proposition->upgroup->coefficient:60,
				'group' => $proposition->group_id,
				'group_text' => $proposition->group_id?$proposition->group->name:'',
				'book_type_group' => $proposition->book_type_group_id,
				'book_type' => $proposition->book_type_id,
				'school_type' => $proposition->school_type,
				'school_level' => $proposition->school_level,
				'school_assignment' => $proposition->school_assignment,
				'school_subject' => $proposition->school_subject_id,
				'school_subject_detailed' => $proposition->school_subject_detailed_id,
				'biblioteca' => $proposition->biblioteca_id
			],
			'market_potential' => [
				'main_target' => $proposition->main_target,
				'market_potential_documents' => $proposition->documents()->wherePivot('type', 'market_potential')->get()
			],
			'technical_data' => [
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
			],
			'print' => [
				'offers' => $proposition->offers
			],
			'authors_expense' => [
				'expenses' => $proposition->author_expenses,
				'note' => '',
				'other' => $proposition->author_other_expense
			],
			'production_expense' => [
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
				'additional_expense' => $proposition->production_additional_expense
			],
			'marketing_expense' => [
				'expense' => $proposition->marketing_expense,
				'additional_expense' => $proposition->marketing_additional_expense
			],
			'distribution_expense' => [
				'margin' => $proposition->margin
			],
			'layout_expense' => [
				'layout_complexity' => $proposition->layout_complexity,
				'layout_include' => $proposition->layout_include,
				'layout_note' => $proposition->layout_note,
				'design_complexity' => $proposition->design_complexity,
				'design_include' => $proposition->design_include,
				'design_note' => $proposition->design_note,
			],
			'deadline' => [
				'date' => $proposition->deadline,
				'priority' => $proposition->priority
			],
			'owner' => $proposition->owner,
			'created_at' => $proposition->created_at,
			'deleted_at' => $proposition->deleted_at
		];
		return $out;
	}

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
			'basic_data', 'translation', 'start'
		];
		$out = [];
		if (in_array($step, $allowed_steps)) {
			$function = 'get' . str_replace(' ', '', ucfirst(str_replace('_', ' ',$step)));
			$out = $this->$function($proposition);
		}

		return response()->json($out);
	}

	public function setPropositionStep(Request $request, $id, $step) {
		$proposition = BookProposition::withTrashed()->find($id);
		$allowed_steps = [
			'basic_data', 'translation', 'start'
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
			'note' => $proposition->notes()->where('type', '=', 'start')
		];
	}

	private function setStart(Request $request, BookProposition $proposition) {
		$proposition->project_number = $request->input('project_number');
		$proposition->project_name = $request->input('project_name');
		$proposition->additional_project_number = $request->input('additional_project_number');
		$proposition->save();
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
			'note' => $proposition->notes()->where('type', '=', 'basic_data')->get('note')
		];
	}

	private function getCategorization(BookProposition $proposition) {
		return [
			'supergroup' => $proposition->supergroup,
			'upgroup' => $proposition->upgroup,
			'group' => $proposition->group,
			'book_type_group' => $proposition->book_type_group,
			'book_type' => $proposition->book_type,
			'school_type' => $proposition->school_type,
			'school_level' => $proposition->school_level,
			'school_assignment' => $proposition->school_assignment,
			'school_subject' => $proposition->school_subject,
			'school_subject_detailed' => $proposition->school_subject_detailed,
			'biblioteca' => $proposition->biblioteca,
			'note' => $proposition->notes()->where('type', '=', 'categorization')->get('note')
		];
	}

	private function getMarketPotential(BookProposition $proposition) {
		return [
			'main_target' => $proposition->main_target,
			'market_potential_documents' => $proposition->documents()->wherePivot('type', 'market_potential')->get(),
			'note' => $proposition->notes()->where('type', '=', 'market_potential')->get('note')
		];
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
			'note' => $proposition->notes()->where('type', '=', 'technical_data')->get('note')
		];
	}

	private function getPrint(BookProposition $proposition) {
		return [
			'offers' => $proposition->offers,
			'note' => $proposition->notes()->where('type', '=', 'print')->get('note')
		];
	}

	private function getAuthorsExpense(BookProposition $proposition) {
		return [
			'expenses' => $proposition->author_expenses,
			'other' => $proposition->author_other_expense,
			'note' => $proposition->notes()->where('type', '=', 'authors_expense')->get('note')
		];
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
			'note' => $proposition->notes()->where('type', '=', 'production_expense')->get('note')
		];
	}

	private function getMarketingExpense(BookProposition $proposition) {
		return [
			'expense' => $proposition->marketing_expense,
			'additional_expense' => $proposition->marketing_additional_expense,
			'note' => $proposition->notes()->where('type', '=', 'marketing_expense')->get('note')
		];
	}

	private function getDistributionExpense(BookProposition $proposition) {
		return [
			'margin' => $proposition->margin,
			'note' => $proposition->notes()->where('type', '=', 'distribution_expense')->get('note')
		];
	}

	private function getLayoutExpense(BookProposition $proposition) {
		return [
			'layout_complexity' => $proposition->layout_complexity,
			'layout_include' => $proposition->layout_include,
			'layout_note' => $proposition->layout_note,
			'design_complexity' => $proposition->design_complexity,
			'design_include' => $proposition->design_include,
			'design_note' => $proposition->design_note,
			'note' => $proposition->notes()->where('type', '=', 'layout_expense')->get('note')
		];
	}

	private function getDeadline(BookProposition $proposition) {
		return [
			'date' => $proposition->deadline,
			'priority' => $proposition->priority,
			'note' => $proposition->notes()->where('type', '=', 'deadline')->get('note')
		];
	}

	private function getCompare(BookProposition $proposition) {
		return $proposition->expenses;
	}

	private function setCompare(Request $request, $proposition) {
		$proposition->expenses = $request->input('expenses');
		$proposition->save();
	}

	private function setBasicData(Request $request, $proposition) {
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
			if (!$proposition->documents->contains($document['id'])) {
				$proposition->documents()->save( $file, [ 'type' => 'manuscript' ] );
			}
		}
		$authors = [];
		foreach ($request->input('authors') as $author) {
			$authors[] = $author['id'];
		}
		$proposition->authors()->sync($authors);
		$proposition->save();
		return true;
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