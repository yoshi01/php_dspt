<?php
namespace App\Lib\Facade;


class ItemDao
{
    private static $instance;
    private $items;
    private function __construct()
    {
        $fp = fopen(APP. 'Lib/Facade/item_data.txt', 'r');

        /**
         * ヘッダ行を抜く
         */
        $dummy = fgets($fp, 4096);

        $this->items = [];
        while ($buffer = fgets($fp, 4096)) {
            $item_id = trim(substr($buffer, 0, 10));
            $item_name = trim(substr($buffer, 10, 20));
            $item_price = trim(substr($buffer, 30));

            $item = new Item($item_id, $item_name, $item_price);
            $this->items[$item->getId()] = $item;
        }

        fclose($fp);
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new ItemDao();
        }
        return self::$instance;
    }

    public function findById($itemId)
    {
        if (array_key_exists($itemId, $this->items)) {
            return $this->items[$itemId];
        } else {
            return null;
        }
    }

    public function setAside(OrderItem $order_item)
    {
        echo $order_item->getItem()->getName() . 'の在庫引当をおこないました<br>';
    }

    /**
     * このインスタンスの複製を許可しないようにする
     * @throws \RuntimeException
     */
    public final function __clone() {
        throw new \RuntimeException ('Clone is not allowed against ' . get_class($this));
    }
}
