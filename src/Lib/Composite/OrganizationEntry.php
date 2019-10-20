<?php
namespace App\Lib\Composite;

abstract class OrganizationEntry
{
    private $code;
    private $name;

    public function __construct($code, $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    public abstract function add(OrganizationEntry $entry);

    public function dump()
    {
        echo $this->code . ":" . $this->name . "<br>\n";
    }
}
