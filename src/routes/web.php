<?php


use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth'], 'namespace' => 'Inspirium\BookProposition\Controllers',], function() {

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
	Route::any('{id}/{all}', function() {
		return view(config('app.template') . '::router-view');
	});
	Route::any('{all}', function() {
		return view(config('app.template') . '::router-view');
	});

   /* Route::get('basic_data', function() {
        return view(config('app.template') . '::proposition.basic_data');
    });
    Route::get('market_potential', function() {
        return view(config('app.template') . '::proposition.market_potential');
    });
    Route::get('technical_data', function() {
        return view(config('app.template') . '::proposition.technical_data');
    });
    Route::get('print', function() {
        return view(config('app.template') . '::proposition.print');
    });
    Route::get('authors_expense', function() {
        return view(config('app.template') . '::proposition.authors_expense');
    });
    Route::get('production_expense', function() {
        return view(config('app.template') . '::proposition.production_expense');
        return view(config('app.template') . '::proposition.production_expense');
    });
    Route::get('marketing_expense', function() {
        return view(config('app.template') . '::proposition.marketing_expense');
    });
    Route::get('distribution_expense', function() {
        return view(config('app.template') . '::proposition.distribution_expense');
    });
    Route::get('layout_expense', function() {
        return view(config('app.template') . '::proposition.layout_expense');
    });
    Route::get('deadline', function() {
        return view(config('app.template') . '::proposition.deadline');
    });
    Route::get('calculation', function() {
        return view(config('app.template') . '::proposition.calculation');
    });
    Route::get('precalculation', function() {
        return view(config('app.template') . '::proposition.precalculation');
    });
   */


});

});
