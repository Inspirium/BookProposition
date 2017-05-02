<?php


use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Inspirium\BookProposition\Controllers', 'middleware' => ['web', 'auth'], 'prefix' => 'proposition'], function() {
    Route::get('basic_data', function() {
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
});
