<?php

namespace Shippa;

use Shippa\Parcel;

abstract class Shipment
{
    public     $api_key,
        $carrier,
        $collection = [],
        $delivery = [],
        $alternative = null,
        $sender = [],
        $receiver = [],
        $unique_id = null,
        $items = [],
        $shipment_type = 'PARCEL',
        $dropoff = false,
        $manual_booking = false,
        $customer_reference = null,
        $collection_date = null,
        $estimated_value = 0,
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
        $url = null,
        $contents = null,
        $test = false,
        $terms = 'DAP',
        $dutiable = false,
        $pallets = null,
        $commercial_invoice_base_64 = null,
        $no_plt = false,

        $return_raw = false,
        $return_test_success = false,
        $return_test_error = false,
        $label_format = 'A4';

    protected function __construct($key = '', $api_url = null)
    {
        if ($key) {
            $this->api_key = $key;
        }

        if ($api_url) {
            $this->url = $api_url;
        }
    }

    protected function setCarrier($carrier)
    {
        $this->carrier = $carrier;
    }

    protected function validateShipment()
    {
        if (empty($this->collection_date)) {
            throw new \Exception("No collection date specified.");
        }

        if (empty($this->customer_reference)) {
            throw new \Exception("No customer reference specified.");
        }

        if (empty($this->items) || count($this->items) < 1) {
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

    public function setCollectionData(array $data = [])
    {
        if (!empty($data["country_code"]) && $data["country_code"] == "IE" && empty($data["address_3"])) {
            $data["address_3"] =  $data["county"];
        }
        $this->collection = $data;
    }

    public function setDeliveryData(array $data = [])
    {
        if (!empty($data["country_code"]) && $data["country_code"] == "IE" && empty($data["address_3"])) {
            $data["address_3"] =  $data["county"];
        }
        $this->delivery = $data;
    }

    public function setAlternativeCollectionData(array $data = [])
    {
        if (!empty($data["country_code"]) && $data["country_code"] == "IE" && empty($data["address_3"])) {
            $data["address_3"] =  $data["county"];
        }
        $this->alternative = $data;
    }

    public function setReceiverData(array $data = [])
    {
        if (!empty($data["country_code"]) && $data["country_code"] == "IE" && empty($data["address_3"])) {
            $data["address_3"] =  $data["county"];
        }
        $this->receiver = $data;
    }

    public function setSenderData(array $data = [])
    {
        if (!empty($data["country_code"]) && $data["country_code"] == "IE" && empty($data["address_3"])) {
            $data["address_3"] =  $data["county"];
        }
        $this->sender = $data;
    }

    public function setCustomsData(array $data = [])
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
        $this->collection_date = date('d-m-Y', strtotime($collection_date)) . ' 00:00:00';
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

    public function setContents($contents)
    {
        $this->contents = $contents;
    }

    public function setTest($test = false)
    {
        $this->test = $test;
    }

    public function setDutiable($dutiable)
    {
        $this->dutiable = $dutiable;
    }

    public function setTrackingNumber($tracking_number)
    {
        $this->tracking_number = $tracking_number;
        $this->tracking_reference = $tracking_number;
    }

    public function setCustomsCurrency($currency)
    {
        $this->customs_data['currency'] = $currency;
    }

    public function setCustomsReason($reason)
    {
        $this->customs_data['reason'] = $reason;
    }

    public function setCustomsFullReason($fullReason)
    {
        $this->customs_data['full_reason'] = $fullReason;
    }

    public function setCustomsExporterVat($vat)
    {
        $this->customs_data['sender_vat'] = $vat;
    }

    public function setCustomsExporterEori($eori)
    {
        $this->customs_data['exporter_eori'] = $eori;
    }

    public function setCustomsImporterVat($vat)
    {
        $this->customs_data['receiver_vat'] = $vat;
    }

    public function setCustomsImporterEori($eori)
    {
        $this->customs_data['importer_eori'] = $eori;
    }

    public function setCustomsInsuranceValue($insuranceValue)
    {
        $this->customs_data['insurance_value'] = $insuranceValue;
    }

    public function setCustomsShippingValue($shippingValue)
    {
        $this->customs_data['shipping_value'] = $shippingValue;
    }

    public function setCustomsWeightUnit($weightUnit)
    {
        $this->customs_data['weight_unit'] = $weightUnit;
    }

    public function setCustomsInvoiceNumber($invoiceNumber)
    {
        $this->customs_data['invoice_number'] = $invoiceNumber;
    }

    public function setCustomsTor($tor)
    {
        $this->customs_data['tor'] = $tor;
    }

    public function setCustomsIoss($ioss)
    {
        $this->customs_data['ioss'] = $ioss;
    }

    public function setCustomsClearanceInfo($clearanceInfo)
    {
        $this->customs_data['clearance_info'] = $clearanceInfo;
    }

    public function setCustomsExporter($exporter)
    {
        $this->customs_data['exporter'] = $exporter;
    }

    public function setCustomsImporter($importer)
    {
        $this->customs_data['importer'] = $importer;
    }

    public function setCustomsExportItems($exportItems)
    {
        $this->customs_data['export_line_items'] = $exportItems;
    }

    public function setCustomsPaperwork($customsPaperwork)
    {
        $this->commercial_invoice_base_64 = $customsPaperwork;
    }

    public function setEstimatedValue($estimatedValue)
    {
        $this->estimated_value = $estimatedValue;
    }

    public function setTerms($terms)
    {
        $this->terms = $terms;
    }

    public function setPallets($pallets)
    {
        $this->pallets = $pallets;
    }

    public function setNoPlt($noPlt)
    {
        $this->no_plt = $noPlt;
    }

    public function setLabelFormat($format = 'A4')
    {
        $this->label_format = $format;
    }

    public function doShipmentCreate()
    {
        if (!$this->url) {
            throw new \Exception("No API Url set", 500);
        }
        $this->validateShipment();

        $this->booking = [
            'customer_reference' => $this->customer_reference,
            'collection_date' => $this->collection_date,
            'from_time' => $this->from_time,
            'to_time' => $this->to_time,
            'tracking_reference' => $this->tracking_reference,
            'collection' => $this->collection,
            'delivery' => $this->delivery,
            'alternate' => $this->alternative,
            'contents' => $this->contents,
            'parcels' => $this->items,
            'notifications' => $this->notifications
        ];

        if (!empty($this->service)) {
            $this->booking['service'] = $this->service;
        }

        if (!empty($this->services)) {
            $this->booking['services'] = $this->services;
        }

        if (!empty($this->sender)) {
            $booking['sender'] = $this->sender;
        }

        if (!empty($this->receiver)) {
            $booking['receiver'] = $this->receiver;
        }

        if (!empty($this->customs_data)) {
            $this->booking['customs'] = $this->customs_data;
            $this->booking['terms'] = $this->terms ?? 'DAP';
        }

        if (!empty($this->pallets)) {
            $booking['pallets'] = $this->pallets;
        }

        if (!empty($this->commercial_invoice_base_64)) {
            $this->booking['commercial_invoice_base_64'] = $this->commercial_invoice_base_64;
        }

        if (!empty($this->no_plt)) {
            $this->booking['no_plt'] = $this->no_plt;
        }

        $headers = array(
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json',
            'Accept: application/json',
        );

        if ($this->unique_id != null) {
            $headers[] = "unique-id: " . $this->unique_id;
        }

        if ($this->test) {
            $headers[] = 'test: 1';
        }

        $jsonData = json_encode($this->booking);
        if ($this->return_raw) {
            return $jsonData;
        }

        if ($this->return_test_success) {
            $obj =  json_decode($this->testShipmentResponseSuccess());
        } else if ($this->return_test_error) {
            $obj =  json_decode($this->testShipmentResponseError());
        } else {


            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url . $this->carrier . '/shipment');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close($ch);
            $obj = json_decode($server_output);
        }

        if (isset($obj->status) && $obj->status == "error") {
            throw new \Exception($this->order_number . " Shipment failed: " . $obj->message);
        } else if (empty($obj->tracking_number)) {
            throw new \Exception($this->order_number . " Shippa Error - No tracking number returned: " . json_encode($obj));
        }

        $this->tracking_number = $obj->tracking_number;

        // $this->getLabel();
        return $obj;
        echo json_encode($this->booking);
    }

    public function doLabelCreate($tracking_number = null)
    {
        if (!$this->url) {
            throw new \Exception("No API Url set", 500);
        }
        $this->validateShipment();

        $this->booking = [
            'customer_reference' => $this->customer_reference,
            'collection_date' => $this->collection_date,
            'from_time' => $this->from_time,
            'to_time' => $this->to_time,
            'tracking_reference' => $this->tracking_reference,
            'estvalue' => $this->estimated_value,
            'collection' => $this->collection,
            'delivery' => $this->delivery,
            'alternate' => $this->alternative,
            'parcels' => $this->items,
            'contents' => $this->contents,
            'notifications' => $this->notifications,
            'dutiable' => $this->dutiable,
            'terms' => $this->terms ?? 'DAP',
            'label_format' => $this->label_format ?? 'A4',
        ];

        if (!empty($this->service)) {
            $this->booking['service'] = $this->service;
        }

        if (!empty($this->services)) {
            $this->booking['services'] = $this->services;
        }

        if (!empty($this->sender)) {
            $booking['sender'] = $this->sender;
        }

        if (!empty($this->receiver)) {
            $booking['receiver'] = $this->receiver;
        }

        if (!empty($this->customs_data)) {
            $this->booking['customs'] = $this->customs_data;
        }

        if (!empty($this->commercial_invoice_base_64)) {
            $this->booking['commercial_invoice_base_64'] = $this->commercial_invoice_base_64;
        }

        if (!empty($this->no_plt)) {
            $this->booking['no_plt'] = $this->no_plt;
        }

        $headers = array(
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json',
            'Accept: application/json',
        );

        if ($this->unique_id != null) {
            $headers[] = "unique-id: " . $this->unique_id;
        }

        if ($this->test) {
            $headers[] = 'test: 1';
        }

        $jsonData = json_encode($this->booking);
        if ($this->return_raw) {
            return $jsonData;
        }

        if ($this->return_test_success) {
            $obj = json_decode($this->testLabelResponseSuccess());
        } else if ($this->return_test_error) {
            $obj = json_decode($this->testLabelResponseError());
        } else {

            // dd($this->booking);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url . $this->carrier . '/label');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close($ch);
            $obj = json_decode($server_output);
        }

        return $obj;
    }

    public function doLabelVoid()
    {
        $headers = array(
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json',
            'Accept: application/json',
        );

        if ($this->unique_id != null) {
            $headers[] = "unique-id: " . $this->unique_id;
        }

        if ($this->test) {
            $headers[] = 'test: 1';
        }
        if ($this->return_test_success) {
            $obj = json_decode($this->testLabelDeleteSuccess());
        } else if ($this->return_test_error) {
            $obj = json_decode($this->testLabelDeleteError());
        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url . $this->carrier . '/void/' . $this->tracking_reference);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            $obj = json_decode($server_output);
        }

        if ($obj->status == 'success') {
            return $obj;
        } else if ($obj->status == "error") {
            throw new \Exception("Label Void Failed: " . $obj->message);
        } else if (empty($obj->tracking_number)) {
            throw new \Exception("Label Void Failed - No tracking number returned: " . $obj->message);
        }
    }

    private function getItems()
    {
        $items = [];
        foreach ($this->items as $item) {
            $items[] = (array)$item;
        }

        return $items;
    }

    public function garble()
    {
        file_put_contents('test.txt', json_encode($this));
        // var_dump($this);
    }

    public function getLabel($tracking_number = null)
    {
        if (!$this->url) {
            throw new \Exception("No API Url set", 500);
        }
        if (!empty($this->label)) {
            return $this->label;
        } else {
            if (empty($this->tracking_number)) {
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

            if ($this->test) {
                $headers[] = 'test: 1';
            }


            if ($this->return_raw) {
                return $jsonData = json_encode(['tracking_number' => $this->tracking_number]);
            }

            if ($this->return_test_success) {
                $obj =  json_decode($this->testLabelResponseSuccess());
            } else if ($this->return_test_error) {
                $obj = json_decode($this->testLabelResponseError());
            } else {

                curl_setopt($ch, CURLOPT_URL, $this->url . $this->carrier . '/label/' . $this->tracking_number);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $server_output = curl_exec($ch);

                curl_close($ch);
                $obj = json_decode($server_output);
            }
            if (isset($obj->error)) {
                throw new \Exception("Label Failed (" . $obj->error[0]->errorCode . "): " . $obj->error[0]->errorMessage . "(" . $obj->error[0]->obj . ")", $obj->error[0]->errorCode);
            }

            if ($obj->status === 'error') {
                throw new \Exception($obj->message);
            }
            $this->label = $obj->label;

            // return $this->label;
            return $obj;
        }
    }

    public function getTracking($tracking_number = null, $request = null)
    {
        if (!$this->url) {
            throw new \Exception("No API Url set", 500);
        }
        if (empty($this->tracking_number)) {
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

        if ($this->test) {
            $headers[] = 'test: 1';
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . $this->carrier . '/tracking/' . $this->tracking_number);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        //        var_dump($this->url . '/' . $this->carrier.'/tracking/'.$tracking_number);
        //        var_dump($this->carrier);
        //        var_dump($this->token);

        $server_output = curl_exec($ch);

        $obj = json_decode($server_output);

        if (isset($obj->error)) {
            throw new ShippaException("Tracking Failed (" . $obj->error[0]->errorCode . "): " . $obj->error[0]->errorMessage . "(" . $obj->error[0]->obj . ")", $obj->error[0]->errorCode);
        }

        if (isset($obj->status) && $obj->status === 'error') {
            throw new \Exception($obj->message);
        }

        curl_close($ch);


        // return $obj->events;
        return $obj;
    }

    public function doShipmentCancel($tracking_number = null, $curlPost = false, $data = [])
    {
        if (!$this->url) {
            throw new \Exception("No API Url set", 500);
        }
        if (empty($this->tracking_number)) {
            $this->tracking_number = $tracking_number;
        }

        $headers = array(
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json',
            'Accept: application/json',
        );

        if ($this->test) {
            $headers[] = 'test: 1';
        }

        if ($this->unique_id != null) {
            $headers[] = "unique-id: " . $this->unique_id;
        }

        if ($this->return_raw) {
            return ['tracking_number' => $this->tracking_number];
        }

        if ($this->return_test_success) {
            $obj =  json_decode($this->testShipmentCancelSuccess());
        } else if ($this->return_test_error) {
            $obj =  json_decode($this->testShipmentCancelError());
        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url . $this->carrier . '/cancel/' . $this->tracking_number);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if ($curlPost) {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
            $server_output = curl_exec($ch);

            //mail('sales@parcelbroker.co.uk', 'ParcelForce', print_r($server_output, true));

            curl_close($ch);
            $obj = json_decode($server_output);
        }

        if ($obj->status === 'error') {
            throw new \Exception($obj->message ?? $obj->description);
        }

        return $obj;
    }

    public function getLocations($country_code, $postcode)
    {
        if (!$this->url) {
            throw new \Exception("No API Url set", 500);
        }
        $headers = array(
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json',
            'Accept: application/json',
        );


        if ($this->unique_id != null) {
            $headers[] .= "unique-id: " . $this->unique_id;
        }

        if ($this->test) {
            $headers[] = 'test: 1';
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . $this->carrier . '/locations/' . $country_code . '/' . $postcode);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if ($response === false) {
            throw new \Exception("Locations Lookup Failed(" . curl_error($ch) . ")", 100);
        }

        curl_close($ch);

        $json = json_decode($response);

        return $json->message->locations;
    }

    public function getShipmentData($identier, $number)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json',
            'Accept: application/json',
        );

        if (!in_array($identier, ['tracking_number', 'consignment_number'])) {
            throw new \Exception("Shipment Lookup Failed (Unknown identier " . $identier . ")", 100);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . $this->carrier . '/shipment/' . $identier . '/' . $number);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        if ($response === false) {
            throw new \Exception("Shipment Lookup Failed (" . curl_error($ch) . ")", 100);
        }

        curl_close($ch);

        $json = json_decode($response);
        return $json;
    }

    abstract protected function addService($code, $type = 'collection');
    abstract protected function setServiceReturn($is_return = false);
    abstract protected function setServiceDocument($is_document = false);
    abstract public function testShipmentResponseSuccess();
    abstract public function testShipmentResponseError();
    abstract public function testLabelResponseSuccess();
    abstract public function testLabelResponseError();
    abstract public function testShipmentCancelSuccess();
    abstract public function testShipmentCancelError();
    abstract public function testLabelDeleteSuccess();
    abstract public function testLabelDeleteError();
    /*{
		$this->services[] = ['code' => $code, 'type' => $type];
	}*/
}
