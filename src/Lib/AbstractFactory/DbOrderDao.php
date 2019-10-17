<?php
namespace App\Lib\AbstractFactory;


class DbOrderDao implements OrderDao
{
    private $orders;
    public function __construct(ItemDao $item_dao)
    {
        $fp = fopen(APP . 'Lib/AbstractFactory/order_data.txt', 'r');

        /**
         * ヘッダ行を抜く
         */
        $dummy = fgets($fp, 4096);

        $this->orders = array();
        while ($buffer = fgets($fp, 4096)) {
            $order_id = trim(substr($buffer, 0, 10));
            $item_ids = trim(substr($buffer, 10));

            $order = new Order($order_id);
            foreach (explode(',', $item_ids) as $item_id) {
                $item = $item_dao->findById($item_id);
                if (!is_null($item)) {
                    $order->addItem($item);
                }
            }
            $this->orders[$order->getId()] = $order;
        }

        fclose($fp);
    }

    public function findById($order_id)
    {
        if (array_key_exists($order_id, $this->orders)) {
            return $this->orders[$order_id];
        } else {
            return null;
        }
    }
}

