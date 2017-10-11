<?php

namespace MyCloud\Api\Model;

use MyCloud\Api\Core\MyCloudModel;

/**
 * Class Order
 *
 * Represents a MyCloud Order belonging to a Shop.
 *
 * @package MyCloud\Api\Model
 */
class Order extends MyCloudModel
{
	private $shop = NULL;

	public function getShop()
	{
		return $this->shop;
	}

	public function setShop( $shop )
	{
		$this->shop = $shop;
		return $this;
	}

}
