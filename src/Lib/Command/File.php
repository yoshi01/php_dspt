<?php
namespace App\Lib\Command;


class File
{
    /**
     * @var
     */
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function decompress()
    {
        echo $this->name . 'を展開しました<br>';
    }

    public function compress()
    {
        echo $this->name . 'を圧縮しました<br>';
    }

    public function create()
    {
        echo $this->name . 'を作成しました<br>';
    }
}
