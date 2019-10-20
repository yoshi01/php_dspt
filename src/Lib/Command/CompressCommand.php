<?php
namespace App\Lib\Command;


class CompressCommand implements Command
{
    private $file;
    public function __construct(File $file)
    {
        $this->file = $file;
    }
    public function execute()
    {
        $this->file->compress();
    }
}
