<?php

namespace MyCloud\Api\Model;

use \DateTime;
use MyCloud\Api\Core\MyCloudModel;
use MyCloud\Api\Core\ReflectionUtil;
use MyCloud\Api\Log\MCLoggingManager;

/**
 * Class Order
 *
 * Represents a MyCloud Order belonging to a Shop.
 *
 * @package MyCloud\Api\Model
 */
class Order extends MyCloudModel
{
	// NOTE
	// I could find no way to avoid duplicating these constants between
	// this code and the API webapp. Sigh.
	//
	// So we need to keep these in-sync with the OrderController model
	// class in the API webapp.
	//
	const API_STATUS_RESERVED    = 'RESERVED';
	const API_STATUS_WAITPAYMENT = 'WAITPAY';
	const API_STATUS_RECVPAYMENT = 'RECVPAY';
	const API_STATUS_APPROVED    = 'APPROVED';
	const API_STATUS_PICKING     = 'PICKING';
	const API_STATUS_PROCESSING  = 'PROCESSING';
	const API_STATUS_SHIPPED     = 'SHIPPED';
	const API_STATUS_DELIVERED   = 'DELIVERED';
	const API_STATUS_UNKNOWN     = 'UNKNOWN';

	private $shop = NULL;
	/*
	 * OrderItems are returned with an existing Order
	 */
	private $order_items = array();
	
	private $delivery_mode = NULL;

	/*
	 * Products are added to create a new Order.
	 */
	private $products = array();
	private $attachments = array();

	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	public function getStatus() {
		return $this->status;
	}
	public function setStatus($status) {
		$this->status = $status;
		return $this;
	}

	public function getMcNumber() {
		return $this->mc_number;
	}
	public function setMcNumber($mc_number) {
		$this->mc_number = $mc_number;
		return $this;
	}

	public function getName() {
		return $this->mc_number;
	}
	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	public function getAddress() {
		return $this->address;
	}
	public function setAddress($address) {
		$this->address = $address;
		return $this;
	}

	public function getPostcode() {
		return $this->postcode;
	}
	public function setPostcode($postcode) {
		$this->postcode = $postcode;
		return $this;
	}

	public function getPhoneNumber() {
		return $this->phone;
	}
	public function setPhoneNumber($phone_number) {
		$this->phone_number = $phone_number;
		return $this;
	}

	public function getEmail() {
		return $this->email;
	}
	public function setEmail($email) {
		$this->email = $email;
		return $this;
	}

	public function getWeight() {
		return $this->mc_number;
	}
	public function setWeight($weight) {
		$this->weight = $weight;
		return $this;
	}

	public function getShop()
	{
		if ( $this->shop == NULL ) {
			
		}
		return $this->shop;
	}

	public function addOrderItem( $order_item )
	{
		$this->order_items[] = $order_item;
		return $this;
	}

	public function getOrderItems()
	{
		return $this->order_items;
	}

	public function getAttachments()
	{
		return $this->attachments;
	}

	public function setDeliveryMode( $delivery_mode )
	{
		$this->delivery_mode = $delivery_mode;
		return $this;
	}

	public function addProduct( $product, $quantity, $price )
	{
		$order_item = new OrderItem( $this->id );
		$order_item->product_id = $product->id;
		$order_item->quantity = $quantity;
		$order_item->price = $price;
		$this->addOrderItem( $order_item );
		return $this;
	}

	public function addProducts( $products )
	{
		$this->products = array_merge( $this->products, $products );
		return $this;
	}

	public function attachFile( $attachment, $filename, $filetype, $filepath )
	{
		$this->attachments[] =
			array(
				'attachment' => $attachment,
				'filename'   => $filename,
				'filetype'   => $filetype,
				'filepath'   => $filepath
			);
		return $this;
	}

    public static function all( $params = array(), $apiContext = null )
    {
		$orders = NULL;

		// ArgumentValidator::validate($params, 'params');
        $payLoad = array();
        $allowedParams = array(
            'page_size' => 1,
            'page' => 1,
            // 'start_time' => 1,
            // 'end_time' => 1,
            // 'sort_order' => 1,
            // 'sort_by' => 1,
            // 'total_required' => 1,
        );

        $json_data = self::executeCall(
            "/v1/orders" . "?" . http_build_query(array_intersect_key($params, $allowedParams)),
            "GET",
            $payLoad,
            array(),
            $apiContext
        );

		$result = json_decode( $json_data, true );

		if ( $result['success'] ) {
			if ( is_array($result['data']) ) {
				$orders = array();
				foreach ( $result['data'] as $order_data ) {
					$order = new Order();
					$order->fromArray( $order_data['attributes'] );
					$orders[] = $order;
					if ( is_array($order_data['order_items']) ) {
						foreach ( $order_data['order_items'] as $order_item_data ) {
							$order_item = new OrderItem( $order );
							$order_item->fromArray( $order_item_data['attributes'] );
							$order->addOrderItem( $order_item );
						}
					}
				}
			}
		} else {
			MCLoggingManager::getInstance(__CLASS__)
				->error( "Failed getting Order list: " . $result->message );
		}

        return $orders;
    }

    public static function get( $orderId, $apiContext = null )
    {
		$order = NULL;

        $payLoad = array();
        $json_data = self::executeCall(
            "/v1/orders/" . $orderId,
            "GET",
            $payLoad,
            array(),
            $apiContext
        );
		print "Order::get(" . $orderId . ") DATA: " . $json_data . "\n";

		$result = json_decode( $json_data, true );

		if ( $result['success'] ) {
			$data = $result['data'];
			$order = new Order();
			$order->fromArray( $data['attributes'] );
			$order_items_data = $data['order_items'];
			if ( is_array($order_items_data) ) {
				foreach ( $order_items_data as $order_item_data ) {
					$attrs = $order_item_data['attributes'];
					$order_item = new OrderItem( $order );
					$order_item->fromArray($attrs);
					$order->order_items[] = $order_item;
				}
			}
		} else {
			MCLoggingManager::getInstance(__CLASS__)
				->error( "Failed getting Order list: " . $result->message );
		}

        return $order;
    }

    public function create( $apiContext = null )
    {
        $payload = $this->toArray();

		$payload['delivery_mode_id'] = empty($this->delivery_mode) ? '0' : $this->delivery_mode->id;

		$index  = 0;
		foreach ( $this->order_items as $order_item ) {
			$payload['order_items[' . $index . '][product_id]'] = $order_item->product_id;
			$payload['order_items[' . $index . '][quantity]'] = $order_item->quantity;
			$payload['order_items[' . $index . '][price]'] = $order_item->price;
			$index++;
		}

		$index  = 0;
		foreach ( $this->attachments as $attach ) {
			$payload['attach_name[' . $index . ']'] = $attach['attachment'];
			$payload['attach_file[' . $index . ']'] =
				new \CurlFile(
					$attach['filepath'],
					$attach['filetype'],
					$attach['filename']
				);
			$index++;
		}
		// print "CREATE ORDER: PAYLOAD: " . var_export($payload, true) . PHP_EOL;

        $json_data = self::executeCall(
            "/v1/orders",
            "POST",
            $payload,
            array(),
            $apiContext
        );

		// print "CREATE ORDER: JSON RESULT: " . $json_data . PHP_EOL;

        // $this->fromJson( $json_data );
		$result = json_decode( $json_data );
		if ( ! $result->success ) {
			MCLoggingManager::getInstance(__CLASS__)
				->error( "Failed creating Order: " . $result->message );
		}

        return $result->order_id;
    }

}
