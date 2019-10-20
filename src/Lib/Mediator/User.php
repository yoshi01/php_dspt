<?php


namespace App\Lib\Mediator;


class User
{
    private $chatroom;
    private $name;
    public function __construct($name)
    {
        $this->name = $name;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setChatroom(Chatroom $value)
    {
        $this->chatroom = $value;
    }
    public function getChatroom()
    {
        return $this->chatroom;
    }

    public function sendMessage($to, $message)
    {
        $this->chatroom->sendMessage($this->name, $to, $message);
    }

    public function receiveMessage($from, $message)
    {
        printf('<font color="008800">%sさんから%sさんへ</font>： %s<hr>', $from, $this->getName(), $message);
    }
}
