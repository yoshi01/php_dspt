<?php


namespace App\Lib\Proxy;


class ItemDaoProxy
{
    private $dao;
    private $cache;

    public function __construct(ItemDao $dao)
    {
        $this->dao = $dao;
        $this->cache = [];
    }

    public function findById($item_id)
    {
        if (array_key_exists($item_id, $this->cache)) {
            echo '<font color="#dd0000">Proxyで保持しているキャッシュからデータを返します</font><br>';
            return $this->cache[$item_id];
        }

        $this->cache[$item_id] = $this->dao->findById($item_id);
        return $this->cache[$item_id];
    }

}
