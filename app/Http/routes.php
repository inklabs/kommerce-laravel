<?php

Route::get('/', function () {
    return view('welcome');
});


Route::resource('/p', 'ProductController', ['only' => 'show']);
