<?php


namespace App\Lib\State;


class User
{
    private $name;
    private $state;
    private $count = 0;

    public function __construct($name)
    {
        $this->name = $name;

        // 初期値
        $this->state = UnauthorizedState::getInstance();
        $this->resetCount();
    }

    public function switchState()
    {
        echo "状態遷移:" . get_class($this->state) . "→";
        $this->state = $this->state->nextState();
        echo get_class($this->state) . "<br>";
        $this->resetCount();
    }
    public function isAuthenticated()
    {
        return $this->state->isAuthenticated();
    }

    public function getMenu()
    {
        return $this->state->getMenu();
    }

    public function getUserName()
    {
        return $this->name;
    }

    public function getCount()
    {
        return $this->count;
    }

    public function incrementCount()
    {
        $this->count++;
    }

    public function resetCount()
    {
        $this->count = 0;
    }
}
