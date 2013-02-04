<?php

// suggested setup

Route::get('/', function() {
    return View::make('fancy::auto');
});

Route::get('{name}', function($name) {
    return View::make('fancy::auto');
})
->where('name', '(.?.+?)(/[0-9]+)?/?$');
