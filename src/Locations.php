<?php

namespace Shippa;

class Locations
{
    protected $api_key;
    protected $url;
    public $unique_id;
    public $carrier = '';

    public function __construct($key = '', $api_url = null)
    {
        if ($key) {
            $this->api_key = $key;
        }
        /// url
        if ($api_url) {
            $this->url = $api_url;
        } else {
            throw new \Exception('Shippa API URL not set');
        }
    }

    public function getLocations($country_code, $postcode, $carrier = '', $unique_id = '')
    {
        $headers = [
            'Authorization: Bearer ' . $this->api_key,
            'Accept: application/json',
            'Content-Type: application/json'
        ];

        if ($unique_id != null) {
            $headers[] = 'unique-id: ' . $unique_id;
        }

        $url = rtrim($this->url, '/') . '/' . strtolower($carrier) . '/locations/' . $country_code . '/' . $postcode;
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($c);

        if ($response === false) {
            throw new \Exception("Locations Lookup Failed(" . curl_error($c) . ")", 100);
        }

        curl_close($c);

        $json = json_decode($response);

        return $json;
    }
}
