<?php

namespace App\Lib\Composite;


class Group extends OrganizationEntry
{
    private $entries;

    public function __construct($code, $name)
    {
        parent::__construct($code, $name);
        $this->entries = [];
    }

    public function add(OrganizationEntry $entry)
    {
        array_push($this->entries, $entry);
    }

    public function dump()
    {
        parent::dump();
        foreach ($this->entries as $entry) {
            $entry->dump();
        }
    }

}
