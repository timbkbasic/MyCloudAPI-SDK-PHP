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
class OrderItem extends MyCloudModel
{
	private Order $order = NULL;

	public function getOrder()
	{
		return $this->order;
	}

	public function setOrder( $order )
	{
		$this->order = $order;
		return $this;
	}

}
