<?php
namespace App\Lib\Builder;


class NewsDirector
{
    private $builder;
    private $url;

    public function __construct(NewsBuilder $builder, $url)
    {
        $this->builder = $builder;
        $this->url = $url;
    }

    public function getNews()
    {
        return $this->builder->parse($this->url);
    }
}
