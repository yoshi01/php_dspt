<?php
namespace App\Lib\AbstractFactory;


class DbFactory implements DaoFactory
{
    public function createItemDao()
    {
        return new DbItemDao();
    }

    public function createOrderDao()
    {
        // TODO: Implement createOrderDao() method.
        return new DbOrderDao($this->createItemDao());
    }
}
