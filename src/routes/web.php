<?php


use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Inspirium\BookProposition\Controllers', 'middleware' => 'web', 'prefix' => 'proposition'], function() {
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
});
