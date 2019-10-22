<?php


namespace App\Lib\Strategy;


class ReadTabSeparatedDataStrategy extends ReadItemDataStrategy
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
            list($item_code, $item_name, $price, $release_date) = explode("\t", $buffer);

            /**
             * 戻り値のオブジェクトの作成
             */
            $obj            = new \stdClass();
            $obj->item_name = $item_name;
            $obj->item_code = $item_code;
            $obj->price     = $price;

            list($year, $mon, $day) = explode('/', $release_date);
            $obj->release_date = mktime(0, 0, 0,
                $mon,
                $day,
                $year);

            $return_value[] = $obj;
        }

        fclose($fp);

        return $return_value;
    }
}

