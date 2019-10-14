<?php
namespace App\Lib\Factory;

class ReaderFactory
{
    public function create($filename)
    {
        return $this->createReader($filename);
    }

    private function createReader($filename)
    {
        $poscsv = stripos($filename, '.csv');
        $posxml = stripos($filename, '.xml');

        if ($poscsv !== false) {
            return new CSVFileReader($filename);
        } elseif ($posxml !== false) {
            return new XMLFileReader($filename);
        } else {
            die('This filename is not supported : ' . $filename);
        }
    }
}
