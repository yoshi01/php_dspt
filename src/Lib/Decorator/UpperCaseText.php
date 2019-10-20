<?php


namespace App\Lib\Decorator;


class UpperCaseText extends TextDecorator
{
    public function __construct(Text $target)
    {
        parent::__construct($target);
    }

    public function getText()
    {
        return mb_strtoupper(parent::getText());
    }
}
