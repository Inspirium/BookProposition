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
    Route::get('authors_expense', function() {
        return view(config('app.template') . '::proposition.authors_expense');
    });
    Route::get('production_expense', function() {
        return view(config('app.template') . '::proposition.production_expense');
    });
    Route::get('marketing_expense', function() {
        return view(config('app.template') . '::proposition.marketing_expense');
    });
    Route::get('distribution_expense', function() {
        return view(config('app.template') . '::proposition.distribution_expense');
    });
});
