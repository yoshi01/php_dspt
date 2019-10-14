<?php
namespace App\Lib\Template;

/**
 * Class AbstractDisplay
 *
 * @package App\Lib\Template
 */
abstract class AbstractDisplay
{
    /**
     * @var array
     */
    private $data;

    /**
     * AbstractDisplay constructor.
     *
     * @param $data
     */
    public function __construct($data)
    {
        if (!is_array($data)) {
            $data = [$data];
        }
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    public function display()
    {
        $this->displayHeader();
        $this->displayBody();
        $this->displayFooter();
    }

    protected abstract function displayHeader();

    protected abstract function displayBody();

    protected abstract function displayFooter();
}
