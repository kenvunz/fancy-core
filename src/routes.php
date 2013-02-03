<?php

use Fancy\Core\Facade\Core;

Route::get('{name}', function($name) {
    return View::make('fancy::auto');
})
->where('name', '(.?.+?)(/[0-9]+)?/?$');
