<?php
namespace App\Lib\Facade;

class OrderItem
{
    private $item;
    private $amount;

    public function __construct(Item $item, $amount)
    {
        $this->item = $item;
        $this->amount = $amount;
    }

    /**
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }
}
