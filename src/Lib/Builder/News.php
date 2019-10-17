<?php
namespace App\Lib\Builder;


class News
{
    private $title;
    private $url;
    private $target_data;

    public function __construct($title, $url, $target_data)
    {
        $this->title = $title;
        $this->url= $url;
        $this->target_data = $target_data;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->target_data;
    }
}
