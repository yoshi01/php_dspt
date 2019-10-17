<?php
namespace App\Lib\Bridge;


class Listing
{
    private $data_source;

    public function __construct($data_source)
    {
        $this->data_source = $data_source;
    }

    public function oepn()
    {
        $this->data_source->open();
    }

    /**
     * データソースからデータを取得する
     * @return array データの配列
     */
    function read() {
        return $this->data_source->read();
    }

    /**
     * データソースを閉じる
     */
    function close() {
        $this->data_source->close();
    }
}
