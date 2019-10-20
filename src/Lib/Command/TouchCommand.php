<?php
namespace App\Lib\Command;


class TouchCommand implements Command
{
    private $file;
    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function execute()
    {
        $this->file->create();
    }
}
