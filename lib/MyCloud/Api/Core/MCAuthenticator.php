<?php

namespace MyCloud\Api\Core;

use MyCloud\Api\Log\MCLoggingManager;
use SimpleJWT\JWT;
use SimpleJWT\Keys\KeySet;
use SimpleJWT\InvalidTokenException;

/**
 * Class MCAuthenticator
 *
 * This class will handle the authentication model for making
 * requests to the server. In this API, we use JWT.
 *
 * @package MyCloud\Api\Core
 */
class MCAuthenticator
{

    /**
     * @var ApiContext
     */
    private $apiContext;

    /**
     * LoggingManager
     *
     * @var MCLoggingManager
     */
    private $logger;

    /**
     * Default Constructor
     *
     * @param ApiContext       $apiContext
     * @param array            $config
     * @throws MCConfigurationException
     */
    public function __construct( $apiContext )
    {
		$this->apiContext = $apiContext;
        $this->logger = MCLoggingManager::getInstance(__CLASS__);
    }

	public function getToken()
	{
		$api_key = '';
		$secret_key = '';
		$this->logger->debug( str_repeat('-', 128) );
		$this->logger->debug( "Getting Authentication Token" );
		$config = $this->apiContext->getConfig();
        if ( isset($config['acct.apiKey']) ) {
            $api_key = $config['acct.apiKey'];
		}
        if ( isset($config['acct.secretKey']) ) {
            $secret_key = $config['acct.secretKey'];
		}

		$token = NULL;
		// NOTE This value must be coordinated with the API server:
		$set = KeySet::createFromSecret('vMOFTi3fANea9fSCP8GcWbq67BFpr6Xz');
		$token_cache = TokenCache::pull( $this->apiContext->getConfig(), $api_key );
		if ( ! empty($token_cache) ) {
			$token = $token_cache['token'];
			$this->logger->debug( "TokenCache: " . $token );
		}

		if ( ! empty($token) ) {
			try {
			    $jwt = JWT::decode( $token, $set, 'HS256' );
				$iat = $jwt->getClaim('iat');
				$exp = $jwt->getClaim('exp');
				$iatTime = \DateTime::createFromFormat( 'U', $iat );
				$expTime = \DateTime::createFromFormat( 'U', $exp );
				$dateUTC = new \DateTime(null, new \DateTimeZone("UTC"));
				$this->logger->debug( "Current UTC Time: " . $dateUTC->format('Y-m-d H:i:s') );
				$this->logger->debug( "Cache Token Issued At: " . $iatTime->format('Y-m-d H:i:s') );
				$this->logger->debug( "Cache Token Expires At: " . $expTime->format('Y-m-d H:i:s') );
				$now = $dateUTC->getTimestamp();
				if ( $now >= ($exp - 60) ) {
					// UNDONE Delete from cache!
					$this->logger->debug( "Cache Token has expired already." );
					$token = NULL;
				}
			} catch ( InvalidTokenException $ex ) {
				// NOTE
				// It appears that the JWT library is smart enough to throw an exception
				// for is when the token has already expired:
				//    InvalidTokenException: Too late due to exp claim
				//
				// UNDONE Delete from cache!
				$token = NULL;
				$this->logger->debug( "Cache Token invalid: " . $ex->getMessage() );
			}
		}

		if ( empty($token) ) { // UNDONE
			$this->logger->debug( "Requesting New Token" );
			$path = '/v1/gettoken';
			$method = 'POST';
	        $config = $this->apiContext->getConfig();
			$httpConfig = new MCHttpConfig( $path, $method, $config );

			$http = new MCHttpConnection( $this->apiContext, $httpConfig, NULL );
			$parameters = array( 'apikey' => $api_key, 'secretkey' => $secret_key );

// FIXME Need to try exception here.
			$json_data = $http->execute( $path, $method, $parameters, NULL );

			$this->logger->debug( "Requested Token data: " . $json_data );
			$token_data = json_decode( $json_data, true );
			$token = $token_data['token'];
			$this->logger->debug( "Requested Token: " . $token );

			$tokenIssued = 0;
			$tokenExpires = 0;
			try {
			    $jwt = JWT::decode($token, $set, 'HS256');
				$iat = $jwt->getClaim('iat');
				$tokenIssued = $iat;
				$exp = $jwt->getClaim('exp');
				$tokenExpires = $exp;
				$iatTime = \DateTime::createFromFormat( 'U', $iat );
				$expTime = \DateTime::createFromFormat( 'U', $exp );
				$dateUTC = new \DateTime(null, new \DateTimeZone("UTC"));
			} catch ( InvalidTokenException $ex ) {
				$token = NULL;
				$this->logger->debug( "Requested Token invalid: " . $ex->getMessage() );
			}
			
			TokenCache::push( $this->apiContext->getConfig(), $api_key, $token, $tokenIssued, $tokenExpires );
		}

		$this->logger->debug( "Return Token: " . $token );
		return $token;
	}

}
