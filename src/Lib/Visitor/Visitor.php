<?php


namespace App\Lib\Visitor;


interface Visitor
{
    public function visit(OrganizationEntry $entry);
}
