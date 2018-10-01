<?php

namespace Shippa;

class Ras
{

	protected 	$ras_fetch = false,
				$api_key = null,
				$ras_type = false,
				$ras_carrier_data = [],
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


	public function getRas($country_code, $postcode, $carrier = '')
	{
		$url = $this->url;

		if($carrier) {
			$url .= "/{$carrier}";
		}

		$url .= "/ras/{$country_code}/{$postcode}";


		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_HTTPHEADER, [
			'Authorization: Bearer ' . $this->api_key,
			'Accept: application/json',
			'Content-Type: application/json'
		]);

		$ret= curl_exec ($c);
		curl_close($c);
		$this->ras_fetch = true;
		$ras = json_decode($ret);

		if("none" !== $ras[0]) {
			$this->ras_type = $ras[0];
			foreach($ras[1] as $carrier_data) {
				$this->ras_carrier_data[$carrier_data->carrier_name] = $carrier_data;
			}
		}
	}

	public function __call($method, $parameters)
	{
		if(!$this->ras_fetch) {
			throw new \Exception("No RAS lookup data");
		}
		$method_pre = substr($method, 0, 3);
		$carrier =  substr($method, 3);
		if("has" === $method_pre) {
			// Check whether carrier has been returned
			return $this->has(strtolower($carrier));
		} else if("get" === $method_pre) {
			// return data associated to carrier
			return $this->get(strtolower($carrier));
		} else {
			throw new \Exception("Method {$method}: not found");
		}

	}

	public function get($carrier)
	{
		$carrier = strtolower($carrier);
		if($this->has($carrier)) {
			return $this->ras_carrier_data[$carrier];
		}

		return false;
	}

	public function has($carrier)
	{
		$carrier = strtolower($carrier);
		return !empty($this->ras_carrier_data[$carrier]);
	}

}