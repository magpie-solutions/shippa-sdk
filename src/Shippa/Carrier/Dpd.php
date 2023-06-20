<?php

namespace Shippa\Carrier;

use Shippa\Shipment;

class Dpd extends Shipment
{

    private    $collection_delivery_keys = [
        'name' => true,
        'company' => false,
        'address_1' => true,
        'address_2' => false,
        'address_3' => false,
        'town' => true,
        'county' => false,
        'postcode' => false,
        'country_code' => true,
        'phone' => false,
        'email' => false,
        'instructions' => false
    ];

    public function __construct($key, $url = null)
    {
        parent::__construct($key, $url);
        $this->setCarrier('dpd');
    }

    public function validateShipment()
    {

        parent::validateShipment();

        foreach ($this->collection_delivery_keys as $key => $required) {
            if ($required && empty($this->collection[$key])) {
                throw new \Exception("Collection data missing {$key}");
            } else if (empty($this->collection[$key])) {
                $this->collection[$key] = '';
            }

            if ($required && empty($this->delivery[$key])) {
                throw new \Exception("Delivery data missing {$key}");
            } else if (empty($this->delivery[$key])) {
                $this->delivery[$key] = '';
            }
        }

        if (empty($this->service) || empty($this->service['code'])) {
            throw new \Exception("No service specified");
        }

        if (!in_array($this->service['type'], ['delivery', 'collection'])) {
            throw new \Exception("Service type '{$this->service['type']}' is not a valid service type");
        }
    }

    public function addService($code, $type = 'collection')
    {
        $this->service = ['code' => $code, 'type' => $type];
    }

    public function setServiceReturn($is_return = false)
    {
        $this->service['return'] = $is_return;
    }

    public function setServiceDocument($is_document = false)
    {
        $this->service['document'] = $is_document;
    }


    public function testShipmentResponseSuccess()
    {
        $tracking = str_pad(rand(0, 999999), 6, 0);
        return '{"status":"ok","code":200,"tracking_number":"TST' . $tracking . '"}';
    }

    public function testShipmentResponseError()
    {
        return '{"status":"error"}';
    }

    public function testLabelResponseSuccess()
    {
        return '{"status":"ok","code":200,"label":"<div><h1 style=\"text-position:center\">This is a text</h1></div>"}';
    }

    public function testLabelResponseError() // NEED CONFIRMATION
    {
        return '{"status":"error"}';
    }

    public function testShipmentCancelSuccess() // NEED CONFIRMATION
    {
        return '{"status":"success"}';
    }

    public function testShipmentCancelError() // NEED CONFIRMATION
    {
        return '{"status":"error", "message" : "Could not cancel shipment."}';
    }

    public function testLabelDeleteSuccess() // NEED CONFIRMATION
    {
        return '{"status":"success"}';
    }

    public function testLabelDeleteError() // NEED CONFIRMATION
    {
        return '{"status":"error", "description" : "Could not cancel label."}';
    }
}
