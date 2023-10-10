<?php

namespace Shippa\Carrier;

use Shippa\Shipment;

class Dsv extends Shipment
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
        $this->setCarrier('dsv');
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

    public function setTimedService($timed = '')
    {
        $this->service['timed'] = $timed;
    }

    public function testShipmentResponseSuccess()
    {
        $tracking = str_pad(rand(0, 9999), 4, 0);
        return '{"status":"success","code":200,"tracking_number":"' . $tracking . '"}';
    }

    public function testShipmentResponseError() // Need confirmation
    {
        return '{"status":"error"}';
    }

    public function testLabelResponseSuccess()
    {
        $tracking = str_pad(rand(0, 999999), 6, 0);
        $shipment_id = str_pad(rand(0, 9999), 4, 0);
        return '{"status":"ok","code":200,"data":{"status":"success", "shipment_id":"' . $shipment_id . '","tracking_number":"' . $tracking . '","label":"JVBERi0xLjINJeLjz9MNCjMgMCBvYmoNPDwgDS9MaW5lYXJpemVkIDEgDS9PIDUgDS9IIFsgNzYwIDE1NyBdIA0vTCAzOTA4IA0vRSAzNjU4IA0vTiAxIA0vVCAzNzMxIA0+PiANZW5kb2JqDSAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB4cmVmDTMgMTUgDTAwMDAwMDAwMTYgMDAwMDAgbg0KMDAwMDAwMDY0NCAwMDAwMCBuDQowMDAwMDAwOTE3IDAwMDAwIG4NCjAwMDAwMDEwNjggMDAwMDAgbg0KMDAwMDAwMTIyNCAwMDAwMCBuDQowMDAwMDAxNDEwIDAwMDAwIG4NCjAwMDAwMDE1ODkgMDAwMDAgbg0KMDAwMDAwMTc2OCAwMDAwMCBuDQowMDAwMDAyMTk3IDAwMDAwIG4NCjAwMDAwMDIzODMgMDAwMDAgbg0KMDAwMDAwMjc2OSAwMDAwMCBuDQowMDAwMDAzMTcyIDAwMDAwIG4NCjAwMDAwMDMzNTEgMDAwMDAgbg0KMDAwMDAwMDc2MCAwMDAwMCBuDQowMDAwMDAwODk3IDAwMDAwIG4NCnRyYWlsZXINPDwNL1NpemUgMTgNL0luZm8gMSAwIFIgDS9Sb290IDQgMCBSIA0vUHJldiAzNzIyIA0vSURbPGQ3MGY0NmM1YmE0ZmU4YmQ0OWE5ZGQwNTk5YjBiMTUxPjxkNzBmNDZjNWJhNGZlOGJkNDlhOWRkMDU5OWIwYjE1MT5dDT4+DXN0YXJ0eHJlZg0wDSUlRU9GDSAgICAgIA00IDAgb2JqDTw8IA0vVHlwZSAvQ2F0YWxvZyANL1BhZ2VzIDIgMCBSIA0vT3BlbkFjdGlvbiBbIDUgMCBSIC9YWVogbnVsbCBudWxsIG51bGwgXSANL1BhZ2VNb2RlIC9Vc2VOb25lIA0+PiANZW5kb2JqDTE2IDAgb2JqDTw8IC9TIDM2IC9GaWx0ZXIgL0ZsYXRlRGVjb2RlIC9MZW5ndGggMTcgMCBSID4+IA1zdHJlYW0NCkiJYmBg4GVgYPrBAAScFxiwAQ4oLQDE3FDMwODHwKkyubctWLfmpsmimQ5AEYAAAwC3vwe0DWVuZHN0cmVhbQ1lbmRvYmoNMTcgMCBvYmoNNTMgDWVuZG9iag01IDAgb2JqDTw8IA0vVHlwZSAvUGFnZSANL1BhcmVudCAyIDAgUiANL1Jlc291cmNlcyA2IDAgUiANL0NvbnRlbnRzIDEwIDAgUiANL01lZGlhQm94IFsgMCAwIDYxMiA3OTIgXSANL0Nyb3BCb3ggWyAwIDAgNjEyIDc5MiBdIA0vUm90YXRlIDAgDT4+IA1lbmRvYmoNNiAwIG9iag08PCANL1Byb2NTZXQgWyAvUERGIC9UZXh0IF0gDS9Gb250IDw8IC9UVDIgOCAwIFIgL1RUNCAxMiAwIFIgL1RUNiAxMyAwIFIgPj4gDS9FeHRHU3RhdGUgPDwgL0dTMSAxNSAwIFIgPj4gDS9Db2xvclNwYWNlIDw8IC9DczUgOSAwIFIgPj4gDT4+IA1lbmRvYmoNNyAwIG9iag08PCANL1R5cGUgL0ZvbnREZXNjcmlwdG9yIA0vQXNjZW50IDg5MSANL0NhcEhlaWdodCAwIA0vRGVzY2VudCAtMjE2IA0vRmxhZ3MgMzQgDS9Gb250QkJveCBbIC01NjggLTMwNyAyMDI4IDEwMDcgXSANL0ZvbnROYW1lIC9UaW1lc05ld1JvbWFuIA0vSXRhbGljQW5nbGUgMCANL1N0ZW1WIDAgDT4+IA1lbmRvYmoNOCAwIG9iag08PCANL1R5cGUgL0ZvbnQgDS9TdWJ0eXBlIC9UcnVlVHlwZSANL0ZpcnN0Q2hhciAzMiANL0xhc3RDaGFyIDMyIA0vV2lkdGhzIFsgMjUwIF0gDS9FbmNvZGluZyAvV2luQW5zaUVuY29kaW5nIA0vQmFzZUZvbnQgL1RpbWVzTmV3Um9tYW4gDS9Gb250RGVzY3JpcHRvciA3IDAgUiANPj4gDWVuZG9iag05IDAgb2JqDVsgDS9DYWxSR0IgPDwgL1doaXRlUG9pbnQgWyAwLjk1MDUgMSAxLjA4OSBdIC9HYW1tYSBbIDIuMjIyMjEgMi4yMjIyMSAyLjIyMjIxIF0gDS9NYXRyaXggWyAwLjQxMjQgMC4yMTI2IDAuMDE5MyAwLjM1NzYgMC43MTUxOSAwLjExOTIgMC4xODA1IDAuMDcyMiAwLjk1MDUgXSA+PiANDV0NZW5kb2JqDTEwIDAgb2JqDTw8IC9MZW5ndGggMzU1IC9GaWx0ZXIgL0ZsYXRlRGVjb2RlID4+IA1zdHJlYW0NCkiJdJDBTsMwEETv\/oo92ohuvXHsJEeggOCEwDfEIU1SCqIJIimIv2dthyJVQpGc0Xo88+xzL5beZ0DgN4IIq6oCzd8sK43amAyK3GKmTQV+J5YXo4VmjDYNYyOW1w8Ez6PQ4JuwfAkJyr+yXNgSSwt+NU+4Kp+rcg4uy9Q1a6MdarLcpgvUeUGh7RBFSLk1f1n+5FgsHJaZttFqA+tKLJhfZ3kEY+VcoHuUfvui2O3kCL9COSwk1Ok3deMEd6srUCVa2Q7Nftf1Ewar5a4nfxuu4v59NcLMGAKXlcjMLtwj1BsTQCITUSK52cC3IoNGDnto6l5VmEv4YAwjO8VWJ+s2DSeGttw\/qmA\/PZyLu3vY1p9p0MGZIs2iHdZxjwdNSkzedT0pJiW+CWl5H0O7uu2SB1JLn8rHlMkH2F+\/xa20Rjp+nAQ39Ec8c1gz7KJ4T3H7uXnuwvSWl178CDAA\/bGPlAplbmRzdHJlYW0NZW5kb2JqDTExIDAgb2JqDTw8IA0vVHlwZSAvRm9udERlc2NyaXB0b3IgDS9Bc2NlbnQgOTA1IA0vQ2FwSGVpZ2h0IDAgDS9EZXNjZW50IC0yMTEgDS9GbGFncyAzMiANL0ZvbnRCQm94IFsgLTYyOCAtMzc2IDIwMzQgMTA0OCBdIA0vRm9udE5hbWUgL0FyaWFsLEJvbGQgDS9JdGFsaWNBbmdsZSAwIA0vU3RlbVYgMTMzIA0+PiANZW5kb2JqDTEyIDAgb2JqDTw8IA0vVHlwZSAvRm9udCANL1N1YnR5cGUgL1RydWVUeXBlIA0vRmlyc3RDaGFyIDMyIA0vTGFzdENoYXIgMTE3IA0vV2lkdGhzIFsgMjc4IDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMjc4IDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgDTAgMCAwIDAgMCA3MjIgMCA2MTEgMCAwIDAgMCAwIDAgMCAwIDAgNjY3IDAgMCAwIDYxMSAwIDAgMCAwIDAgMCANMCAwIDAgMCAwIDAgNTU2IDAgNTU2IDYxMSA1NTYgMCAwIDYxMSAyNzggMCAwIDAgODg5IDYxMSA2MTEgMCAwIA0wIDU1NiAzMzMgNjExIF0gDS9FbmNvZGluZyAvV2luQW5zaUVuY29kaW5nIA0vQmFzZUZvbnQgL0FyaWFsLEJvbGQgDS9Gb250RGVzY3JpcHRvciAxMSAwIFIgDT4+IA1lbmRvYmoNMTMgMCBvYmoNPDwgDS9UeXBlIC9Gb250IA0vU3VidHlwZSAvVHJ1ZVR5cGUgDS9GaXJzdENoYXIgMzIgDS9MYXN0Q2hhciAxMjEgDS9XaWR0aHMgWyAyNzggMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDI3OCAwIDI3OCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCANMCAwIDAgNjY3IDAgMCAwIDAgMCAwIDAgMjc4IDAgMCAwIDAgMCAwIDAgMCA3MjIgMCAwIDAgMCAwIDAgMCAwIA0wIDAgMCAwIDAgMCA1NTYgNTU2IDUwMCA1NTYgNTU2IDI3OCAwIDU1NiAyMjIgMCAwIDIyMiA4MzMgNTU2IDU1NiANNTU2IDAgMzMzIDUwMCAyNzggNTU2IDUwMCAwIDAgNTAwIF0gDS9FbmNvZGluZyAvV2luQW5zaUVuY29kaW5nIA0vQmFzZUZvbnQgL0FyaWFsIA0vRm9udERlc2NyaXB0b3IgMTQgMCBSIA0+PiANZW5kb2JqDTE0IDAgb2JqDTw8IA0vVHlwZSAvRm9udERlc2NyaXB0b3IgDS9Bc2NlbnQgOTA1IA0vQ2FwSGVpZ2h0IDAgDS9EZXNjZW50IC0yMTEgDS9GbGFncyAzMiANL0ZvbnRCQm94IFsgLTY2NSAtMzI1IDIwMjggMTAzNyBdIA0vRm9udE5hbWUgL0FyaWFsIA0vSXRhbGljQW5nbGUgMCANL1N0ZW1WIDAgDT4+IA1lbmRvYmoNMTUgMCBvYmoNPDwgDS9UeXBlIC9FeHRHU3RhdGUgDS9TQSBmYWxzZSANL1NNIDAuMDIgDS9UUiAvSWRlbnRpdHkgDT4+IA1lbmRvYmoNMSAwIG9iag08PCANL1Byb2R1Y2VyIChBY3JvYmF0IERpc3RpbGxlciA0LjA1IGZvciBXaW5kb3dzKQ0vQ3JlYXRvciAoTWljcm9zb2Z0IFdvcmQgOS4wKQ0vTW9kRGF0ZSAoRDoyMDAxMDgyOTA5NTUwMS0wNycwMCcpDS9BdXRob3IgKEdlbmUgQnJ1bWJsYXkpDS9UaXRsZSAoVGhpcyBpcyBhIHRlc3QgUERGIGRvY3VtZW50KQ0vQ3JlYXRpb25EYXRlIChEOjIwMDEwODI5MDk1NDU3KQ0+PiANZW5kb2JqDTIgMCBvYmoNPDwgDS9UeXBlIC9QYWdlcyANL0tpZHMgWyA1IDAgUiBdIA0vQ291bnQgMSANPj4gDWVuZG9iag14cmVmDTAgMyANMDAwMDAwMDAwMCA2NTUzNSBmDQowMDAwMDAzNDI5IDAwMDAwIG4NCjAwMDAwMDM2NTggMDAwMDAgbg0KdHJhaWxlcg08PA0vU2l6ZSAzDS9JRFs8ZDcwZjQ2YzViYTRmZThiZDQ5YTlkZDA1OTliMGIxNTE+PGQ3MGY0NmM1YmE0ZmU4YmQ0OWE5ZGQwNTk5YjBiMTUxPl0NPj4Nc3RhcnR4cmVmDTE3Mw0lJUVPRg0="}}';
    }

    public function testLabelResponseError()
    {
        return '{"status":"error","code":3,"message":"Missing or invalid ship to address line 1"}';
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
