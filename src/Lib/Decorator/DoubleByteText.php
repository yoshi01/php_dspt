<?php


namespace App\Lib\Decorator;


class DoubleByteText extends TextDecorator
{
    public function __construct(Text $target)
    {
        parent::__construct($target);
    }

    public function getText()
    {
        return mb_convert_kana(parent::getText(), "RANSKV");
    }
}
