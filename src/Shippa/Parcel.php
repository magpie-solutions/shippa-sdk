<?php

namespace Shippa;

class Parcel
{
	public 	$length = 0,
			$width = 1,
			$height = 0,
			$weight = 0,
			$uom = 'CM',
			$uow = 'KG',
			$size = null;

	public function __construct($length, $width, $height, $weight)
	{
		$this->length = $length;
		$this->width = $width;
		$this->height = $height;
		$this->weight = $weight;
	}

	public function calculateSize($country_to_code = "GB", $country_from_code = "GB")
	{

	}

}