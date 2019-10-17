<?php
namespace App\Lib\AbstractFactory;


interface DaoFactory
{
    public function createItemDao();
    public function createOrderDao();
}
