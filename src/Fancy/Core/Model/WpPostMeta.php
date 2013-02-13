<?php namespace Fancy\Core\Model;

use Illuminate\Database\Eloquent\Model;

class WpPostMeta extends WpModel
{

    protected $primaryKey = 'meta_id';
    protected $table = 'wp_postmeta';
}
