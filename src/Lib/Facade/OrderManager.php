<?php
namespace App\Lib\Facade;

class OrderManager
{
    public static function order(Order $order)
    {
        foreach ($order->getItems() as $orderItem) {
            ItemDao::getInstance()->setAside($orderItem);
        }

        OrderDao::createOrder($order);
    }
}
