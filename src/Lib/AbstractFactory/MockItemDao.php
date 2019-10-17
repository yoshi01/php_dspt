<?php
namespace App\Lib\AbstractFactory;

class MockItemDao implements ItemDao
{
    public function findById($itemId)
    {
        $item = new Item('99', 'ダミー商品');
        return $item;
    }
}
