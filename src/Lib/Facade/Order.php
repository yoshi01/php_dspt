<?php
namespace App\Lib\Facade;


class Order
{
    private $items;

    public function __construct()
    {
        $this->items = [];
    }

    public function addItem(OrderItem $orderItem)
    {
        $this->items[$orderItem->getItem()->getId()] = $orderItem;
    }

    public function getItems()
    {
        return $this->items;
    }
}
