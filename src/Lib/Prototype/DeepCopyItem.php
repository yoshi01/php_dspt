<?php


namespace App\Lib\Prototype;


class DeepCopyItem extends ItemPrototype
{
    protected function __clone()
    {
        $this->setDetail(clone $this->getDetail());
    }
}
