<?php
namespace App\Lib\Builder;


class RssNewsBuilder implements NewsBuilder
{
    public function parse($url)
    {
        $data = simplexml_load_file($url);
        if ($data === false) {
            throw new \Exception('read data faild');
        }
        $list = array();
        foreach ($data->item as $item) {
            $dc = $item->children('http://purl.org/dc/elements/1.1/');
            $list[] = new News($item->title,
                $item->link,
                $dc->date);
        }
        return $list;
    }
}
