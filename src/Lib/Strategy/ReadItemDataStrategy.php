<?php


namespace App\Lib\Strategy;


abstract class ReadItemDataStrategy
{
    private $filename;

    /**
     * コンストラクタ
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function getData()
    {
        if (!is_readable($this->getFilename())) {
            throw new \Exception('file [' . $this->getFilename() . '] is not readable !');
        }

        return $this->readData($this->getFilename());
    }

    public function getFilename()
    {
        return $this->filename;
    }

    protected abstract function readData($filename);
}
