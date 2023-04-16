<?php

namespace App\Helpers;

class Helper
{
    public static function readCsv(string $path)
    {
        $data = [];
        if (($open = fopen($path, "r")) !== FALSE) {

            while (($fdata = fgetcsv($open, 1000, ",")) !== FALSE) {
                $data[] = $fdata;
            }

            fclose($open);
        }
        return $data;
    }
}