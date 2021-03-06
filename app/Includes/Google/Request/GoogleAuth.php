<?php namespace App\Includes\Google\Request;

use App\Includes\Exceptions\CurlErrorException;
use App\Includes\Exceptions\RequestException;
use App\Includes\Exceptions\ServerErrorException;
use Curl\Curl;
use League\OAuth2\Client\Provider\Google;


class GoogleAuth{

    // Google auth access state
    const STATUS_ON = 'ON';
    const STATUS_OFF = 'OFF';

    // Authorization URLs
    const AUTH_URL  = 'https://accounts.google.com/o/oauth2/v2/auth';
    const TOKEN_URL = 'https://www.googleapis.com/oauth2/v4/token';

    // https://github.com/php-curl-class/php-curl-class

    /**
     * Get new token access using authorization code flow
     *
     * @param $code String Authorization code for request token access
     * @return object
     * @throws CurlErrorException
     */
    public static function authorizeRequest($code){
        // Get cURL resource
        try{  $curl = new Curl();  }catch (\ErrorException $e) { throw new CurlErrorException($e->getMessage()); }
        $curl->post(self::TOKEN_URL, array(
            'code'          => $code,
            'client_id'     => CLIENT_ID,
            'client_secret' => CLIENT_SECRET,
            'redirect_uri'  => REDIRECT_URI,
            'grant_type'    => 'authorization_code',
        ));

        // TODO: handle error codes
        if ( $curl->error )
            throw new CurlErrorException("Request Error: " . $curl->errorCode . ' - ' . $curl->errorMessage );

        $response = $curl->response;
        $curl->close();
        return $response;
    }


    /**
     * @param $refreshToken
     * @return object
     * @throws CurlErrorException
     */
    public static function refreshToken($refreshToken){
        // Get cURL resource
        try{  $curl = new Curl();  }catch (\ErrorException $e) { throw new CurlErrorException($e->getMessage()); }
        $curl->post(self::TOKEN_URL, array(
            'client_id'     => CLIENT_ID,
            'client_secret' => CLIENT_SECRET,
            'refresh_token' => $refreshToken,
            'grant_type'    => 'refresh_token',
        ));

        if ( $curl->error )
            throw new CurlErrorException("Request Error: " . $curl->errorCode . ' - ' . $curl->errorMessage );

        $response = $curl->response;
        $curl->close();
        return $response;
    }

    public static function revokeAuth(){
        // https://accounts.google.com/o/oauth2/revoke?token={token}
    }


    /**
     * @param $scopes array
     * @return string
     */
    public static function getAuthUrl($scopes){
        $params = [
            'access_type'   => 'offline',
            'state'         => STATE,
            'scope'         => implode(',', $scopes),
            'response_type' => 'code',
            'redirect_uri'  => REDIRECT_URI
        ];
        return self::AUTH_URL."?".
            "scope=".$params['scope']."&".
            "access_type=".$params['access_type']."&".
            "include_granted_scopes=true&".
            "state=".$params['state']."&".
            "redirect_uri=".$params['redirect_uri']."&".
            "response_type=".$params['response_type']."&".
            "client_id=".CLIENT_ID;
    }

}