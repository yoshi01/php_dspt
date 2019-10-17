<?php
namespace App\Lib\AbstractFactory;


interface ItemDao
{
    public function findById($itemId);
}
