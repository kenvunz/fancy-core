<?php

Route::get('{name}', function($name) {
    return View::make('fancy::content');
})
->where('name', '(.?.+?)(/[0-9]+)?/?$');
