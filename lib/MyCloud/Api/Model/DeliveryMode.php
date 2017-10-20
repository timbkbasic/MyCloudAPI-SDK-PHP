<?php

namespace MyCloud\Api\Model;

use MyCloud\Api\Core\MyCloudModel;

/**
 * Class Product
 *
 * Represents a MyCloud DeliveryMode
 *
 * @package MyCloud\Api\Model
 */
class DeliveryMode extends MyCloudModel
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
            "/v1/deliverymodes" . "?" . http_build_query(array_intersect_key($params, $allowedParams)),
            "GET",
            $payLoad,
            array(),
            $apiContext
        );
		print "DeliveryMode::all() DATA: " . $json_data . "\n";
		$modes_data = json_decode( $json_data, true );
		if ( is_array($modes_data) ) {
			foreach ( $modes_data as $mode_data ) {
				$attrs = $mode_data['attributes'];
				$delivery_mode = new DeliveryMode();
				$delivery_mode->fromArray($attrs);
				$result[] = $delivery_mode;
			}
		}

        return $result;
    }

}
