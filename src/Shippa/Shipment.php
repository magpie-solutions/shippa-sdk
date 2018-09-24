<?php

namespace Shippa;

use Shippa\Parcel;

abstract class Shipment
{
	protected 	$api_key,
				$carrier,
				$collection = [],
				$delivery = [],
				$unique_id,
				$items = [],
				$shipment_type = 'PARCEL',
				$dropoff = false,
				$manual_booking = false,
				$customer_reference = null,
				$collection_date = null,
				$notifications = ['email' => [], 'sms' => []],
				$tracking_reference,
				$from_time = null,
				$to_time = null,
				$services,
				$service,
				$booking = [];

	protected function __construct($key = '')
	{
		if($key) {
			$this->api_key = $key;
		}
	}

	protected function setCarrier($carrier)
	{
		$this->carrier = $carrier;
	}

	protected function validateBooking()
	{
		if(empty($this->collection_date)) {
			throw new \Exception("No collection date specified.");
		}

		if(empty($this->customer_reference)) {
			throw new \Exception("No customer reference specified.");
		}

		if(empty($this->items) || count($this->items) < 1) {
			throw new \Exception("No parcels in the shipment.");
		}
	}

	public function setCollectionData(Array $data = [])
	{
		if(!empty($data["country_code"]) && $data["country_code"] == "IE" && empty($data["line3"])) {
            $data["line3"] =  $data["county"] ;
        }
		$this->collection = $data;
	}

	public function setDeliveryData(Array $data = [])
	{
		if(!empty($data["country_code"]) && $data["country_code"] == "IE" && empty($data["line3"])) {
            $data["line3"] =  $data["county"] ;
        }
		$this->delivery = $data;
	}

	public function addItem($length, $width, $height, $weight)
	{
		$this->items[] = new Parcel($length, $width, $height, $weight);
	}

	public function setManualBooking($manual = false)
	{
		$this->manual_booking = $manual;
	}

	public function setDropoff($dropoff = false)
	{
		$this->dropoff = $dropoff;
	}

	public function setShipmentType($shipment_type = 'PARCEL')
	{
		$this->shipment_type = $shipment_type;
	}

	public function setCustomerReference($customer_reference)
	{
		$this->customer_reference = $customer_reference;
	}

	public function setCollectionDate($collection_date)
	{
		$this->collection_date = date('d-m-Y H:i:s', strtotime($collection_date));
	}

	public function addNotification($notification_info, $type = 'email')
	{
		$this->notifications[$type][] = $notification_info;
	}

	public function setFromTime($time)
	{
		$this->from_time = $time;
	}

	public function setToTime($time)
	{
		$this->to_time = $time;
	}

	public function doBooking()
	{
		$this->validateBooking();

		$this->booking = [
			'customer_reference' => $this->customer_reference,
			'service' => $this->service,
			'collection_date' => $this->collection_date,
			'from_time' => $this->from_time,
			'to_time' => $this->to_time,
			'tracking_reference' => $this->tracking_reference,
			'collection' => $this->collection,
			'delivery' => $this->delivery,
			'parcels' => $this->items,
			'notifications' => $this->notifications
		];

		echo json_encode($this->booking);

	}

	private function getItems()
	{
		$items = [];
		foreach($this->items as $item) {
			$items[] = (Array)$item;
		}

		return $items;
	}

	public function garble()
	{
		var_dump($this);
	}



	abstract protected function addService($code, $type = 'delivery');
	/*{
		$this->services[] = ['code' => $code, 'type' => $type];
	}*/
}