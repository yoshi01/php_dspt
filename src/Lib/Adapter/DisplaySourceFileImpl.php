<?php
namespace App\Lib\Adapter;

/**
 * Class DisplaySourceFileImpl
 *
 * @package App\Lib\Adapter
 */
class DisplaySourceFileImpl extends ShowFile implements DisplaySourceFile
{
    public function __construct($filename)
    {
        parent::__construct($filename);
    }

    public function display()
    {
        parent::showHighlight();
    }
}
