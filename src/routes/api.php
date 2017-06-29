<?php

Route::group(['middleware' => ['api', 'auth:api'], 'namespace' => 'Inpirium\BookProposition\Controllers', 'prefix' => 'api/proposition'], function() {
    Route::post('/', function() {
       return response()->json(['id' => 1]);
    });
    Route::patch('{id?}', function() {
        return response()->json([]);
    });
});
