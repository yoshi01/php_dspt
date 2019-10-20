<?php
namespace App\Lib\Command;


class CopyCommand implements Command
{
    /**
     * @var File
     */
    private $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }
    public function execute()
    {
        $file = new File('copy_of_' . $this->file->getName());
        $file->create();
    }
}
