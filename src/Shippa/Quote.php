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
				$url = null,
				$service_reference = null,
				$url_end_point = '/quote',
				$price_groups = [];

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

	public function setServiceReference($code)
	{
		$this->service_reference = $code;
		$this->url_end_point = '/quote/service';
		return $this;
	}

	public function setCollectionDate($date)
	{
		$this->collection_date = $date;
		return $this;
	}

	public function setCollection($location, $country_code)
	{
		if(!$location) {
			$location = '*';
		}
		if($country_code && $location) {
			$this->collection = [
				'location' => $location,
				'country_code' => self::convertForShippa($country_code),
			];
		}

		return $this;
	}

	public function setDelivery($location, $country_code)
	{
		if(!$location) {
			$location = '*';
		}
		if($country_code && $location) {
			$this->delivery = [
				'location' => $location,
				'country_code' => self::convertForShippa($country_code),
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

	public function getCostOnly()
	{
		if($this->service_reference) {
			$this->url_end_point = '/quote/costs/service';
		} else {
			$this->url_end_point = '/quote/costs';
		}
		return $this;
	}

	public function addPriceGroup($price_group)
	{
		$this->price_groups[] = $price_group;
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
			'items' => $this->items,
			'price_groups' => $this->price_groups
		];

		if(!empty($this->service_reference)) {
			$quote['service_reference'] = $this->service_reference;
		}


		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $this->url . $this->url_end_point);
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
		if(isset($ret_json->services)) {
			$this->quoted = $ret_json->services;
		} else if(isset($ret_json->service)) {
			$this->quoted = $ret_json->service;
		} else if(isset($ret_json->message) || isset($ret_json->description)) {
            throw new \Error($ret_json->message ?? $ret_json->description);
            $this->error = $ret_json->message ?? $ret_json->description;
        } else {
			throw new \Error('Something went wrong');
			$this->error = 'Something went wrong';
		}


		return $this;
	}

	public function validateCart($cart_items)
	{
		$data_items = ['items' => $cart_items, 'price_groups' => $this->price_groups];
		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $this->url . '/quote/validate');
		curl_setopt($c, CURLOPT_POST, 1);
		curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($data_items));
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_HTTPHEADER, [
			'Authorization: Bearer ' . $this->api_key,
			'Accept: application/json',
			'Content-Type: application/json',
    		'Content-Length: ' . strlen(json_encode($data_items))
		]);
		$ret= curl_exec ($c);
		curl_close($c);
		return json_decode($ret)->validation;

	}

	public function getQuote()
	{
		return $this->quoted;
	}

	public function error()
	{
		return $this->error;
	}

	public function convertForShippa($code)
	{
		$code = str_replace('UK_', 'GB_', $code);
		if($code == 'GB_IMIS') {
			$code = 'GB_IOM';
		}
		if($code == 'GB_M') {
			$code = 'GB';
		}
		return $code;
	}

	public function getPallets($items, $service_code)
	{
		$data_items = ['items' => $cart_items, 'service_code' => $service_code];
		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $this->url . '/quote/pallet-sizes');
		curl_setopt($c, CURLOPT_POST, 1);
		curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($data_items));
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_HTTPHEADER, [
			'Authorization: Bearer ' . $this->api_key,
			'Accept: application/json',
			'Content-Type: application/json',
    		'Content-Length: ' . strlen(json_encode($data_items))
		]);
		$ret= curl_exec ($c);
		curl_close($c);
		return json_decode($ret)->validation;
	}
}