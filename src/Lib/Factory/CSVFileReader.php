<?php
namespace App\Lib\Factory;

class CSVFileReader implements Reader
{
    private $filename;

    private $handler;

    public function __construct($filename)
    {
        if (!is_readable($filename)) {
            throw new \Exception('file "' . $filename . '" is not readable !');
        }
        $this->filename = $filename;
    }

    public function read()
    {
        $this->handler = fopen($this->filename, "r");
    }

    /**
     * 表示を行ないます
     */
    public function display()
    {
        $column = 0;
        $tmp = "";
        while ($data = fgetcsv ($this->handler, 1000, ",")) {
            $num = count ($data);
            for ($c = 0; $c < $num; $c++) {
                if ($c == 0) {
                    if ($column != 0 && $data[$c] != $tmp) {
                        echo "</ul>";
                    }
                    if ($data[$c] != $tmp) {
                        echo "<b>" . $data[$c] . "</b>";
                        echo "<ul>";
                        $tmp = $data[$c];
                    }
                }else {
                    echo "<li>";
                    echo $data[$c];
                    echo "</li>";
                }
            }
            $column++;
        }
        echo "</ul>";
        fclose ($this->handler);
    }
}
