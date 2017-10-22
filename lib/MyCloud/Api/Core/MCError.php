<?php

namespace MyCloud\Api\Core;

/**
 * Class MCError
 * Placeholder for MyCloud Constants
 *
 * @package MyCloud\Api\Core
 */
class MCError
{
	private $message = NULL;

	public function getMessage()
	{
		return $this->message;
	}

    /**
     * Default Constructor
     *
     */
    public function __construct( $message )
    {
		$this->message = $message;
    }

}
