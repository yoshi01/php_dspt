<?php


namespace App\Lib\Observer;


interface CartListener
{
    public function update(Cart $cart);

}
