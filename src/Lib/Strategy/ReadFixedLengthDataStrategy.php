<?php


namespace App\Lib\Strategy;


class ReadFixedLengthDataStrategy extends ReadItemDataStrategy
{
    protected function readData($filename)
    {
        $fp = fopen($filename, 'r');

        /**
         * ヘッダ行を抜く
         */
        $dummy = fgets($fp, 4096);

        /**
         * データの読み込み
         */
        $return_value = array();
        while ($buffer = fgets($fp, 4096)) {
            $item_name = trim(substr($buffer, 0, 20));
            $item_code = trim(substr($buffer, 20, 10));
            $price = (int)substr($buffer, 30, 8);
            $release_date = substr($buffer, 38);

            /**
             * 戻り値のオブジェクトの作成
             */
            $obj = new \stdClass();
            $obj->item_name = $item_name;
            $obj->item_code = $item_code;
            $obj->price = $price;

            $obj->release_date = mktime(0, 0, 0,
                substr($release_date, 4, 2),
                substr($release_date, 6, 2),
                substr($release_date, 0, 4));

            $return_value[] = $obj;
        }

        fclose($fp);

        return $return_value;
    }
}
