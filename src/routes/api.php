<?php

Route::group(['middleware' => ['api', 'auth:api'], 'namespace' => 'Inspirium\BookProposition\Controllers\Api', 'prefix' => 'api/proposition'], function() {
	Route::get('{id}', 'PropositionController@getProposition');
    Route::post('/', function() {
       return response()->json(['id' => 1]);
    });
    Route::patch('{id?}', function() {
        return response()->json([]);
    });
});
