<?php
/**
 * Created by PhpStorm.
 * User: yoshimoto
 * Date: 2019/10/17
 * Time: 0:02
 */

namespace App\Lib\Iterator;


class Employees implements \IteratorAggregate
{
    private $employees;
    public function __construct()
    {
        $this->employees = new \ArrayObject();
    }

    public function add(Employee $employee)
    {
        $this->employees[] = $employee;
    }

    public function getIterator()
    {
        return $this->employees->getIterator();
    }

}
