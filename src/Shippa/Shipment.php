<?php

namespace Shippa;

use Shippa\Parcel;

abstract class Shipment
{
	protected 	$api_key,
				$carrier,
				$collection = [],
				$delivery = [],
				$unique_id = null,
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
				$booking = [],
				$order_number,
				$customs_data = [],
				$tracking_number,
				$label = null,
				$url = null;

	protected function __construct($key = '', $api_url = null)
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

	public function setOrderNumber($order_number)
	{
		$this->order_number = $order_number;
	}

	public function setUniqueId($unique_id)
	{
		$this->unique_id = $unique_id;
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

	public function setCustomsData(Array $data = [])
	{
		$this->customs_data = $data;
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

	public function doShipmentCreate()
	{
		if(!$this->url) {
			throw new Exception("No API Url set", 500);
		}
		$this->validateShipment();

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

		if(!empty($this->customs_data)) {
			$this->booking['customs'] = $this->customs_data;
		}

		$headers = array(
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json',
            'Accept: application/json',
        );

        if ($this->unique_id != null) {
            $headers[] = "unique-id: " . $this->unique_id;
        }

		$jsonData = json_encode($this->booking);
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . $this->carrier.'/shipment');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
		curl_close ($ch);
        $obj = json_decode($server_output);

        if($obj->status == "error") {
            throw new \Exception($this->order_number . " Shipment failed: ".$obj->message);
        } else if( empty($obj->tracking_number) ) {
            throw new \Exception($this->order_number . " Shippa Error - No tracking number returned: " . $obj->message);
        }

        $this->tracking_number = $obj->tracking_number ;

        $this->getLabel();
        return true;
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

	public function getLabel($tracking_number = null)
	{
		if(!$this->url) {
			throw new Exception("No API Url set", 500);
		}
		if(!empty($this->label)) {
			return $this->label;
		} else {
			if(empty($this->tracking_number)) {
				$this->tracking_number = $tracking_number;
			}

			$ch = curl_init();

            $headers = array(
                'Authorization: Bearer ' . $this->api_key,
                'Content-Type: application/json',
                'Accept: application/json',
            );

            if ($this->unique_id != null) {
                $headers[] .= "unique-id: " . $this->unique_id;
            }

            curl_setopt($ch, CURLOPT_URL, $this->url . $this->carrier.'/label/'.$this->tracking_number);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec ($ch);

            curl_close ($ch);
            $obj = json_decode($server_output);

            if(isset($obj->error)) {
                throw new \Exception("Label Failed (".$obj->error[0]->errorCode."): ".$obj->error[0]->errorMessage."(".$obj->error[0]->obj.")", $obj->error[0]->errorCode);
            }

            if($obj->status === 'error') {
            	throw new \Exception($obj->message);
            }
            $this->label = $obj->label ;

            return $this->label ;
		}
	}

	public function getTracking($tracking_number = null)
	{
		if(!$this->url) {
			throw new Exception("No API Url set", 500);
		}
		if(empty($this->tracking_number)) {
			$this->tracking_number = $tracking_number;
		}
		$headers = [
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        if ($this->unique_id != null) {
            $headers[] .= "unique-id: " . $this->unique_id;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . $this->carrier.'/tracking/'.$this->tracking_number);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

//        var_dump($this->url . '/' . $this->carrier.'/tracking/'.$tracking_number);
//        var_dump($this->carrier);
//        var_dump($this->token);

        $server_output = curl_exec ($ch);

        $obj = json_decode($server_output);

        if(isset($obj->error)) {
            throw new ShippaException("Tracking Failed (".$obj->error[0]->errorCode."): ".$obj->error[0]->errorMessage."(".$obj->error[0]->obj.")", $obj->error[0]->errorCode);
        }

        if(isset($obj->status) && $obj->status === 'error') {
        	throw new \Exception($obj->message);
        }

        curl_close ($ch);


        return $obj->events ;
	}

	public function doShipmentCancel($tracking_number = null)
	{
		if(!$this->url) {
			throw new Exception("No API Url set", 500);
		}
		if(empty($this->tracking_number)) {
			$this->tracking_number = $tracking_number;
		}


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . $this->carrier.'/cancel/' . $this->tracking_number);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json',
            'Accept: application/json',
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);

        //mail('sales@parcelbroker.co.uk', 'ParcelForce', print_r($server_output, true));

        curl_close ($ch);
        $obj = json_decode($server_output);

        return $obj->label;

	}

	public function getLocations($country_code, $postcode)
	{
		if(!$this->url) {
			throw new Exception("No API Url set", 500);
		}
        $headers = array(
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json',
            'Accept: application/json',
        );


        if ($this->unique_id != null) {
            $headers[] .= "unique-id: " . $this->unique_id;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . $this->carrier .'/locations/'.$country_code.'/'.$postcode);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec ($ch);

        if($response === false) {
            throw new \Exception("Locations Lookup Failed(".curl_error( $ch ).")", 100);
        }

        curl_close ($ch);

        $json = json_decode($response);

        return $json->message->locations;
	}



	abstract protected function addService($code, $type = 'delivery');
	/*{
		$this->services[] = ['code' => $code, 'type' => $type];
	}*/
}