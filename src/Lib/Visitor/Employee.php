<?php


namespace App\Lib\Visitor;


class Employee extends OrganizationEntry
{
    public function __construct($code, $name)
    {
        parent::__construct($code, $name);
    }

    public function add(OrganizationEntry $entry)
    {
        throw new \Exception('method not allowed');
    }

    public function getChildren()
    {
        return [];
    }
}
