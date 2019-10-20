<?php
namespace App\Lib\Composite;


class Employee extends OrganizationEntry
{
    public function __construct($code, $name)
    {
        parent::__construct($code, $name);
    }

    public function add(OrganizationEntry $entry)
    {
        throw new \Exception('not allowed');
    }

}
