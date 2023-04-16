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

    public static function payOrder($data) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => config('payment.url'),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }
}