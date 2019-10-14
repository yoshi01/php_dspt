<?php
namespace App\Lib\Singleton;

/**
 * Class Singleton
 *
 * @package App\Lib\Singleton
 */
class SingletonSample
{
    /**
     * @var
     */
    private $id;

    private static $instance;

    private function __construct()
    {
        $this->id = md5(date('r') . mt_rand());
    }

    /**
     * @return SingletonSample
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new SingletonSample();
            echo 'SingletonSample created';
        }

        return self::$instance;
    }

    /**
     * @return string
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     *
     */
    public final function __clone()
    {
        throw new \RuntimeException('clone is not allowed');
    }
}
