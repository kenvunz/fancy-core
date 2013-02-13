<?php namespace Fancy\Core\Model;

use Illuminate\Database\Eloquent\Model;

class WpPost extends WpModel
{
    public $timestamps = false;

    protected $primaryKey = 'ID';
    protected $table = 'wp_posts';

    protected $postType;

    public static function cast($attributes = null, $class = null)
    {
        if(!is_array($attributes)) {
            $postType = $attributes;
            $attributes = array();
        }  else {
            $postType = array_get($attributes, 'post_type');
        }

        // make sure provided class existed
        if(!is_null($class) && !class_exists($class)) {
            throw new \RuntimeException("Provided $class does not exist");
        }

        // if class is not specified, let's try to figure it out by its post_type
        if(is_null($class) && !is_null($postType)) {
            $guessedClass = camel_case($postType);

            if(class_exists($guessedClass)) {
                $class = $guessedClass;
            }
        }

        $instance = is_null($class)? new static($attributes) : new $class($attributes);

        return $instance->setPostType($postType);
    }

    public function factory($attributes = null, $class = null)
    {
        if(is_null($class) || ($this instanceof $class)) {
            if(!is_array($attributes)) {
                $postType = $attributes;
            } else {
                $postType = array_get($attributes, 'post_type');
                $this->fill($attributes);
            }

            return $this->setPostType($postType);
        } else {
            return static::cast($attributes, $class);
        }
    }

    public function newQuery()
    {
        $query = parent::newQuery();

        if(isset($this->postType)) {
            $query->where('post_type', '=', $this->postType);
        }

        return $query;
    }

    public function setPostType($postType)
    {
        // avoid overriding postType
        if(is_null($postType) && (!is_null($this->postType))) {
            return $this;
        }

        $this->postType = $postType;
        return $this;
    }

    public function getPostType()
    {
        return $this->postType;
    }

    public function meta()
    {
        return $this->hasMany('Fancy\Core\Model\WpPostMeta', 'post_id');
    }

    public function get_the($what = 'title')
    {
        $GLOBALS['_post'] = $GLOBALS['post'];

        $GLOBALS['post'] = (object) $this->getAttributes();

        $method = "the_$what";

        ob_start();
        self::$wp->$method();

        $value = ob_get_clean();

        $GLOBALS['post'] = $GLOBALS['_post'];

        unset($GLOBALS['_post']);

        return $value;
    }
}
