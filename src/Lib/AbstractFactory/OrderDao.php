<?php
namespace App\Lib\AbstractFactory;


interface OrderDao
{
    public function findById($orderId);
}
