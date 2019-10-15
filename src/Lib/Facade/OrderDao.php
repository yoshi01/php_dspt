<?php
namespace App\Lib\Facade;


class OrderDao
{
    public static function createOrder(Order $order)
    {
        echo '以下の内容で注文データを作成しました';

        echo '<table border="1">';
        echo '<tr>';
        echo '<th>商品番号</th>';
        echo '<th>商品名</th>';
        echo '<th>単価</th>';
        echo '<th>数量</th>';
        echo '<th>金額</th>';
        echo '</tr>';

        foreach ($order->getItems() as $order_item) {
            echo '<tr>';
            echo '<td>' . $order_item->getItem()->getId() . '</td>';
            echo '<td>' . $order_item->getItem()->getName() . '</td>';
            echo '<td>' . $order_item->getItem()->getPrice() . '</td>';
            echo '<td>' . $order_item->getAmount() . '</td>';
            echo '<td>' . ($order_item->getItem()->getPrice() * $order_item->getAmount()) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
}
