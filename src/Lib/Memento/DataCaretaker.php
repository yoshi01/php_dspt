<?php


namespace App\Lib\Memento;


class DataCaretaker
{
    private $snapshot;

    public function __construct()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    public function setSnapshot($snapshot)
    {
        $this->snapshot = $snapshot;
        $_SESSION['snapshot'] = $this->snapshot;
    }

    public function getSnapshot()
    {
        return $_SESSION['snapshot'] ?? null;
    }
}
