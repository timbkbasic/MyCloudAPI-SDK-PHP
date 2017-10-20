<?php

namespace MyCloud\Api\Model;

use MyCloud\Api\Core\MyCloudModel;

/**
 * Class Product
 *
 * Represents a MyCloud Product that is kept in inventory
 *
 * @package MyCloud\Api\Model
 */
class Product extends MyCloudModel
{

    public static function all( $params = array(), $apiContext = null )
    {
		$result = array();

		// ArgumentValidator::validate($params, 'params');
        $payLoad = "";
        $allowedParams = array(
            'page_size' => 1,
            'page' => 1,
            'start_time' => 1,
            'end_time' => 1,
            'sort_order' => 1,
            'sort_by' => 1,
            'total_required' => 1
        );

        $json_data = self::executeCall(
            "/v1/products" . "?" . http_build_query(array_intersect_key($params, $allowedParams)),
            "GET",
            $payLoad,
            array(),
            $apiContext
        );
		// print "Product::all() DATA: " . $json_data . "\n";
		$products_data = json_decode( $json_data, true );
		if ( is_array($products_data) ) {
			foreach ( $products_data as $product_data ) {
				$attrs = $product_data['attributes'];
				$product = new Product();
				$product->fromArray($attrs);
				$result[] = $product;
			}
		}

        return $result;
    }

}
