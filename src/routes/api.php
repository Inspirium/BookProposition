<?php

Route::group(['middleware' => ['api', 'auth:api'], 'namespace' => 'Inspirium\BookProposition\Controllers\Api', 'prefix' => 'api/proposition'], function() {
	Route::post('assign/{id}', 'PropositionController@assignProposition');
	Route::get('{id}', 'PropositionController@getProposition');
	Route::post('start', 'PropositionController@initProposition');

	Route::get('{id}/files/multimedia', 'PropositionController@getMultimedia');
	Route::post('{id}/files/multimedia', 'PropositionController@setMultimedia');
	Route::get('{id}/files/marketing', 'PropositionController@getMarketing');
	Route::post('{id}/files/marketing', 'PropositionController@setMarketing');

	Route::get('{id}/files/{type}', 'PropositionController@getFiles');
	Route::post('{id}/files/{type}', 'PropositionController@setFiles');

	Route::get('{id}/init', 'PropositionController@getInitData');
	Route::get('{id}/{step}', 'PropositionController@getPropositionStep');
	Route::post('{id}/{step}', 'PropositionController@setPropositionStep');
    Route::post('/', 'PropositionController@saveProposition');
    Route::patch('{id?}', 'PropositionController@saveProposition');
    Route::delete('{id?}', 'PropositionController@deleteProposition');
    Route::post('restore/{id?}', 'PropositionController@restoreProposition');


});
