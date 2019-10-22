<?php


namespace App\Lib\Strategy;


class ItemDataContext
{
    private $strategy;

    public function __construct(ReadItemDataStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function getItemData()
    {
        return $this->strategy->getData();
    }
}
