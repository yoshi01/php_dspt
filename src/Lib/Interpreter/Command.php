<?php


namespace App\Lib\Interpreter;


interface Command
{
    public function execute(Context $context);
}
