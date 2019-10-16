<?php
namespace App\Lib\Iterator;


use Iterator;

class SalesmanIterator extends \FilterIterator
{
    public function __construct(Iterator $iterator)
    {
        parent::__construct($iterator);
    }

    public function accept()
    {
        $employee = $this->getInnerIterator()->current();
        return ($employee->getJob() === 'SALESMAN');
    }

}
