<?php
namespace App\Lib\Observer;


class Cart
{
    private $items;
    private $listeners;

    public function __construct()
    {
        $this->items = [];
        $this->listeners = [];
    }

    public function addItem($item_cd)
    {
        $this->items[$item_cd] = (isset($this->items[$item_cd]) ? ++$this->items[$item_cd] : 1);
        $this->notify();
    }

    public function removeItem($item_cd)
    {
        $this->items[$item_cd] = (isset($this->items[$item_cd]) ? --$this->items[$item_cd] : 0);
        if ($this->items[$item_cd] <= 0) {
            unset($this->items[$item_cd]);
        }
        $this->notify();
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function hasItem($item_cd)
    {
        return array_key_exists($item_cd, $this->items);
    }

    public function addListener(CartListener $listenter)
    {
        $this->listeners[get_class($listenter)] = $listenter;
    }

    public function removeListener(CartListener $listener)
    {
        unset($this->listeners[get_class($listener)]);
    }

    public function notify()
    {
        foreach ($this->listeners as $listener) {
            $listener->update($this);
        }
    }
}
