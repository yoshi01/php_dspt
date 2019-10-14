<?php
namespace App\Lib\Template;

use App\Lib\Template\AbstractDisplay;

/**
 * Class ListDisplay
 *
 * @package App\Lib\Template
 */
class ListDisplay extends AbstractDisplay
{
    protected function displayHeader()
    {
        echo '<dl>';
    }

    protected function displayBody()
    {
        foreach ($this->getData() as $key => $value) {
            echo '<dt>Item ' . $key . '</dt>';
            echo '<dd>' . $value . '</dd>';
        }
    }

    protected function displayFooter()
    {
        echo '</dl>';
    }
}
