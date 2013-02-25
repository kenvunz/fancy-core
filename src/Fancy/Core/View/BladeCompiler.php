<?php namespace Fancy\Core\View;

class BladeCompiler extends \Illuminate\View\Compilers\BladeCompiler
{
    public function compileString($value)
    {
        if(array_search('Loop', $this->compilers) === false) {
            $this->compilers = array_merge(array('Loop'), $this->compilers);
        }

        return parent::compileString($value);
    }

    public function compileLoop($value) {
        $pattern = '/(?<!\w)(\s*)@(loop)(\s*\(.*\))?/';

        $matches = array();
        preg_match($pattern, $value, $matches);

        $arguments = !isset($matches[3])? '()' : preg_replace('/\s*/', '', $matches[3]);

        $replacement = '$1<?php ';

        if($arguments !== '()') {
            $replacement .= '$query = new WP_Query$3; if($query->have_posts()): while($query->have_posts()): $query->the_post()';
        } else {
            $replacement .= 'if(have_posts()): while(have_posts()): the_post()';
        }

        $replacement .= '?>';

        $value = preg_replace($pattern, $replacement, $value);

        $pattern = '/(\s*)@(endloop)(\s*)/';

        $replacement = '$1<?php endwhile; endif;';

        if($arguments !== '()') {
            $replacement .= 'wp_reset_postdata();';
        }

        $replacement .= '?>$3';

        $value = preg_replace($pattern, $replacement, $value);

        return $value;
    }
}
