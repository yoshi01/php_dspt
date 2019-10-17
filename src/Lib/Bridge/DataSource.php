<?php
namespace App\Lib\Bridge;


interface DataSource
{
    public function open();
    public function read();
    public function close();
}
