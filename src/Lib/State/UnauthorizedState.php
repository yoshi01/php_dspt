<?php


namespace App\Lib\State;


class UnauthorizedState implements UserState
{
    private static $singleton = null;

    private function __construct()
    {
    }

    public static function getInstance() {
        if (self::$singleton === null) {
            self::$singleton = new UnauthorizedState();
        }
        return self::$singleton;
    }

    public function isAuthenticated()
    {
        return false;
    }

    public function nextState()
    {
        // 次の状態（認証）を返す
        return AuthorizedState::getInstance();
    }

    public function getMenu()
    {
        $menu = '<a href="?mode=state">ログイン</a>';
        return $menu;
    }

    /**
     * このインスタンスの複製を許可しないようにする
     * @throws \RuntimeException
     */
    public final function __clone() {
        throw new \RuntimeException ('Clone is not allowed against ' . get_class($this));
    }
}
