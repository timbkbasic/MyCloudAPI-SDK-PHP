<?php

namespace MyCloud\Api\Model;

use MyCloud\Api\Core\MyCloudModel;

/**
 * Class OrderItem
 *
 * Represents a MyCloud Order item included in an Order
 *
 * @package MyCloud\Api\Model
 */
class OrderItem extends MyCloudModel
{
	private $order = NULL;

    /**
     * Default Constructor
     *
     */
    public function __construct( $order )
    {
		$this->order = $order;
    }

	public function getOrder()
	{
		return $this->order;
	}

}
