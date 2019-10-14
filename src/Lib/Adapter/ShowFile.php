<?php
namespace App\Lib\Adapter;

/**
 * Class ShowFile
 *
 * @package App\Lib\Adapter
 */
class ShowFile
{
    private $filename;

    public function __construct($filename)
    {
        if (!is_readable($filename)) {
            throw new \Exception('file ' . $filename . 'is not readable');
        }
        $this->filename = $filename;
    }

    public function showPlain()
    {
        echo '<pre>';
        echo htmlspecialchars(file_get_contents($this->filename), ENT_QUOTES, mb_internal_encoding());
        echo '</pre>';
    }

    public function showHighlight()
    {
        highlight_file($this->filename);
    }
}
