<?php


namespace App\Lib\State;


class AuthorizedState implements UserState
{
    private static $singleton = null;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (self::$singleton == null) {
            self::$singleton = new AuthorizedState();
        }
        return self::$singleton;
    }


    public function isAuthenticated()
    {
        return true;
    }

    public function nextState()
    {
        return UnauthorizedState::getInstance();
    }

    public function getMenu()
    {
        $menu = '<a href="?mode=inc">カウントアップ</a> | '
            .    '<a href="?mode=reset">リセット</a> | '
            .    '<a href="?mode=state">ログアウト</a>';
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
