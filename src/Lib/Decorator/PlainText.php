<?php


namespace App\Lib\Decorator;


class PlainText implements Text
{
    private $text = null;

    public function getText()
    {
        return $this->text;
    }

    public function setText($str)
    {
        $this->text = $str;
    }
}
