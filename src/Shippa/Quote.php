<?php

namespace Shippa;

class Quote
{

	protected 	$full = 1,
				$new = 1,
				$shipment_type = 'PARCEL',
				$collection_date = null,
				$collection = null,
				$delivery = null,
				$items = [],
				$api_key,
				$quoted,
				$error = false,
				$url = null;

	public function __construct($key = '', $api_url = null)
	{
		if($key) {
			$this->api_key = $key;
		}

		if($api_url) {
			$this->url = $api_url;
		} else if(defined('SHIPPA_API_URL')) {
			$this->url = SHIPPA_API_URL;
		}
	}

	public function setShipmentType($type)
	{
		$this->shipment_type = $type;
		return $this;
	}

	public function setCollectionDate($date)
	{
		$this->collection_date = $date;
		return $this;
	}

	public function setCollection($location, $country_code)
	{
		if($country_code && $location) {
			$this->collection = [
				'location' => $location,
				'country_code' => $country_code,
			];
		}

		return $this;
	}

	public function setDelivery($location, $country_code)
	{
		if($country_code && $location) {
			$this->delivery = [
				'location' => $location,
				'country_code' => $country_code,
			];
		}

		return $this;
	}

	public function addItem($length, $width, $height, $weight) {
		$this->items[] = [
			'length' => $length,
			'width' => $width,
			'height' => $height,
			'weight' => $weight
		];

		return $this;
	}

	public function doQuote()
	{
		if(!$this->url) {
			throw new \Exception("No API Url set", 500);
		}
		$quote = [
			'full' => 1,
			'new' => 1,
			'collection_date' => $this->collection_date,
			'shipment_type' => $this->shipment_type,
			'collection' => $this->collection,
			'delivery' => $this->delivery,
			'items' => $this->items
		];


		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $this->url . '/quote');
		curl_setopt($c, CURLOPT_POST, 1);
		curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($quote));
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_HTTPHEADER, [
			'Authorization: Bearer ' . $this->api_key,
			'Accept: application/json',
			'Content-Type: application/json',
    		'Content-Length: ' . strlen(json_encode($quote))
		]);
		$ret= curl_exec ($c);
		curl_close($c);

		$ret_json = json_decode($ret);

		if(!empty($ret_json->services)) {
			$this->quoted = $ret_json->services;
		} else {
			$this->error = $ret_json->message;
		}

		return $this;
	}

	public function getQuote()
	{
		return $this->quoted;
	}

	public function error()
	{
		return $this->error;
	}
}