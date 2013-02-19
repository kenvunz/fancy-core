<?php namespace Fancy\Core\Model;

use Illuminate\Database\Eloquent\Model;

class WpPost extends WpModel
{
    public $timestamps = false;

    protected $primaryKey = 'ID';
    protected $table = 'wp_posts';

    protected $postType;

    /**
     * Instantiate a new WpPost model filled with provided attributes
     * @param  string/array $attributes A valid Wordpress 'post_type', or an data array with a 'post_type' key/value
     * @param  string $class            A name of class to be instanstiated from
     * @return WpPost
     */
    public static function cast($attributes = 'post', $class = null)
    {
        if(is_null($attributes)) {
            $attributes = 'post';
        }

        if(is_string($attributes)) {
            $attributes = array('post_type' => $attributes);
        }

        if(!is_array($attributes)) {
            throw new \InvalidArgumentException(
                "The \$attributes paramater must be either a string define a 'post_type' " .
                "or an associated array with a 'post_type' key/value"
            );
        }

        // will default to the default Wordpress 'post_type' ie. 'post'
        $postType = array_get($attributes, 'post_type', 'post');

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

    /**
     * Triggered by Fancy\Core\Support\Factory when resolving an instance
     * Pass-thru the argments to WpPost::cast for instance creationg
     * @see  WpPost::cast
     * @return WpPost
     */
    public function factory($attributes = 'post', $class = null)
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

    public function getThe($what = 'title')
    {
        $_post = $GLOBALS['post'];

        if($_post->ID !== $this->ID) {
            $GLOBALS['_post'] = $_post;
            $post = (object) $this->getAttributes();
            self::$wp->setup_postdata($post);
            $GLOBALS['post'] = $post;
        }

        $method = "the_$what";

        ob_start();
        self::$wp->$method();

        $value = ob_get_clean();

        if(isset($GLOBALS['_post'])) {
            $GLOBALS['post'] = $GLOBALS['_post'];
            unset($GLOBALS['_post']);
        }

        return $value;
    }

    public function getTitleAttribute($value)
    {
        return $this->getPostTitleAttribute($value);
    }

    public function getPostTitleAttribute($value)
    {
        return $this->getThe('title');
    }

    public function getGuidAttribute($value)
    {
        return $this->getThe('guid');
    }

    public function getContentAttribute($value)
    {
        return $this->getPostContentAttribute($value);
    }

    public function getPostContentAttribute($value)
    {
        return $this->getThe('content');
    }

    public function getExcerptAttribute($value)
    {
        return $this->getPostExcerptAttribute($value);
    }

    public function getPostExcerptAttribute($value)
    {
        return $this->getThe('excerpt');
    }
}
