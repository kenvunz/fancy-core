<?php

Route::get('{name}', function($name) {
    return View::make('la-press::content');
})
->where('name', '(.?.+?)(/[0-9]+)?/?$');
