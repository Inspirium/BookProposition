<?php


use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth'], 'namespace' => 'Inspirium\BookProposition\Controllers'], function() {

	Route::get('propositions', 'PropositionController@show');

Route::group(['prefix' => 'proposition'], function() {

    Route::get('/', 'PropositionController@show');
	Route::get('list', function() {
		return view(config('app.template') . '::proposition.list');
	});
	Route::get('proposition', function() {
		return view(config('app.template') . '::proposition.proposition');
	});
	Route::get('work_order', function() {
		return view(config('app.template') . '::proposition.work_order');
	});
	Route::get('documents', function() {
		return view(config('app.template') . '::proposition.documents');
	});
	Route::get('expense', function() {
		return view(config('app.template') . '::proposition.expense');
	});
	Route::get('compare', function() {
		return view(config('app.template') . '::proposition.compare');
	});
	Route::get('task', function() {
		return view(config('app.template') . '::proposition.task');
	});
	Route::get('task_details', function() {
		return view(config('app.template') . '::proposition.task_details');
	});
	Route::get('task_new', function() {
		return view(config('app.template') . '::proposition.task_new');
	});
	Route::get('department-list', function() {
		return view(config('app.template') . '::proposition.department-list');
	});

	Route::get('{id}/edit/print/{offer_id}/{doc_type}', 'Api\PropositionController@getOfferDoc');


	Route::any('{id}/{all}/{step}', function() {
		return view(config('app.template') . '::router-view');
	});
	Route::any('{id}/{all}', function() {
		return view(config('app.template') . '::router-view');
	});
	Route::any('{all}', function() {
		return view(config('app.template') . '::router-view');
	});


});

});
