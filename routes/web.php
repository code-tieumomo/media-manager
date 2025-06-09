<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/media-picker-demo', function () {
    return view('media-picker-demo');
});
