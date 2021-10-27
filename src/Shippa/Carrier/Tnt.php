<?php

namespace Shippa\Carrier;

use Shippa\Shipment;

class Tnt extends Shipment
{

	private	$collection_delivery_keys = [
				'name' => true,
				'company' => false,
				'address_1' => true,
				'address_2' => true,
				'address_3' => false,
				'town' => true,
				'county' => false,
				'postcode' => true,
				'country_code' => true,
				'phone' => false,
				'email' => true,
				'instructions' => false
			];

	public function __construct($key, $url = null) {
		parent::__construct($key, $url);
		$this->setCarrier('tnt');
	}

	public function validateShipment()
	{

		parent::validateShipment();

		foreach($this->collection_delivery_keys as $key => $required) {
			if($required && empty($this->collection[$key])) {
				throw new \Exception("Collection data missing {$key}");
			} else if(empty($this->collection[$key])) {
				$this->collection[$key] = '';
			}

			if($required && empty($this->delivery[$key])) {
				throw new \Exception("Delivery data missing {$key}");
			} else if(empty($this->delivery[$key])) {
				$this->delivery[$key] = '';
			}
		}

		if(empty($this->service) || empty($this->service['code'])) {
			throw new \Exception("No service specified");
		}

		if(!in_array($this->service['type'], ['delivery', 'collection'])) {
			throw new \Exception("Service type '{$this->service['type']}' is not a valid service type");
		}

	}

	public function addService($code, $type = 'delivery')
	{
		$this->service = ['code' => $code, 'type' => $type];
	}


}