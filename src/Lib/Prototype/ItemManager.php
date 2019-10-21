<?php


namespace App\Lib\Prototype;


class ItemManager
{
    private $items;

    public function __construct()
    {
        $this->items = [];
    }

    public function registItem(ItemPrototype $item)
    {
        $this->items[$item->getCode()] = $item;
    }

    public function create($item_code)
    {
        if (!array_key_exists($item_code, $this->items)) {
            throw new \Exception('item_code [' . $item_code . '] not exists !');
        }
        $cloned_item = $this->items[$item_code]->newInstance();

        return $cloned_item;
    }
}
