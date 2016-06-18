<?php

Route::get('links', 'LinksController@attach');
Route::post('links/attach', 'LinksController@attach');
Route::post('links/detach', 'LinksController@detach');
Route::post('links/report', 'LinksController@report');
Route::get('links/paginate', 'LinksController@paginate');
Route::post('links/{id}/delete', 'LinksController@delete');
Route::post('links/delete', 'LinksController@deleteAll');
Route::post('links/{id}/approve', 'LinksController@approve');
Route::post('links/rate', 'LinksController@rate');