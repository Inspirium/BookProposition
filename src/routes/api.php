<?php

Route::group(['middleware' => ['api', 'auth:api'], 'namespace' => 'Inspirium\BookProposition\Controllers\Api', 'prefix' => 'api/proposition'], function() {
	Route::post('start', 'PropositionController@initProposition');

	Route::group(['prefix' => '{id}'], function() {
		Route::get('/', 'PropositionController@getProposition');
		Route::get('init', 'PropositionController@getInitData');

		Route::delete('/', 'PropositionMaintenanceController@deleteProposition');
		Route::post('assign/document', 'PropositionMaintenanceController@assignDocument');
		Route::post('assign', 'PropositionMaintenanceController@assignProposition');
		Route::post('approval', 'PropositionMaintenanceController@approvalProposition');
		Route::post('request_approval', 'PropositionMaintenanceController@requestApproval');
		Route::post('restore', 'PropositionMaintenanceController@restoreProposition');

		Route::get('files/multimedia', 'PropositionController@getMultimedia');
		Route::post('files/multimedia', 'PropositionController@setMultimedia');
		Route::get('files/marketing', 'PropositionController@getMarketing');
		Route::post('files/marketing', 'PropositionController@setMarketing');

		Route::get('files/{type}', 'PropositionController@getFiles');
		Route::post('files/{type}', 'PropositionController@setFiles');

		Route::get('{step}/{type?}', 'PropositionController@getPropositionStep');
		Route::post('{step}/{type?}', 'PropositionController@setPropositionStep');
	});
});
