<?php

Route::group(['middleware' => ['api', 'auth:api'], 'namespace' => 'Inspirium\BookProposition\Controllers\Api', 'prefix' => 'api/proposition'], function() {
	Route::post('assign/{id}', 'PropositionController@assignProposition');
	Route::get('{id}', 'PropositionController@getProposition');
    Route::post('/', 'PropositionController@saveProposition');
    Route::patch('{id?}', 'PropositionController@saveProposition');
    Route::delete('{id?}', 'PropositionController@deleteProposition');
    Route::post('restore/{id?}', 'PropositionController@restoreProposition');
});

\Illuminate\Translation\FileLoader::class;
