<?php namespace Fancy\Core\Support;

class Partial
{

    protected $wp;

    protected $finder;

    public function __construct(Worpdress $wp, ViewFinderInterface $finder)
    {
        $this->wp = $wp;
        $this->finder = $finder;
    }
}
