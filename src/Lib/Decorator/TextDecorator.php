<?php


namespace App\Lib\Decorator;


abstract class TextDecorator implements Text
{
    private $text;

    public function __construct(Text $target)
    {
        $this->text = $target;
    }

    /**
     * インスタンスが保持する文字列を返します
     */
    public function getText()
    {
        return $this->text->getText();
    }

    /**
     * インスタンスに文字列をセットします
     */
    public function setText($str)
    {
        $this->text->setText($str);
    }
}
