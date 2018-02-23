<?php
/**
 * Main class file for the Apify Crawler WordPress Plugin.
 * 
 * @link    https://github.com/tajidyakub/dscrape-wp-plugin
 * @package Apify_crawler/includes
 * @version 1.0.0
 */

// Prevent direct access
if ( !defined('APIFY_CRAWLER_VER') ) {
    die( 'Error while loading the file.');
}

require APIFY_CRAWLER_INCL . 'autoload.php';
use GuzzleHttp\Client as HTTPClient;

/**
 * Apify Crawler class.
 *
 * Init Apify Crawler, populate params, get the action's results ID and
 * PageFunction results by consuming Apify's API Endpoints.
 * 
 * @link    https://github.com/tajidyakub/apify-crawler-wp-plugin
 * @package Apify_crawler/includes
 * @version 1.0.0
 */

class ApifyCrawler {

    protected $client;          // Crawler Object
    protected $params;          // Params
    
    /**
     * Apify Crawler construct
     *
     * Init Guzzle, define client options and populate API parameters.
     * $params = array(
     *  'api' => array(
     *          'base_uri'  => 'base_uri of the API',
     *          'token'     => 'your api token',
     *          'crawler_id'=> 'crawler instance ID',
     *      ),
     *  '
     * );
     * 
     * @param  array $params Array of parameters
     * @return void
     */
    public function __construct( $params ) {
        // Populate configs
        $this->params                = $params;
        $this->params['api']['call'] = "/v1/NEzBHiSvWBW5JzJ9G/crawlers/{$params['api']['crawler_id']}/execute?token={$params['api']['token']}";

        // Init Guzzle
        $this->client = new HTTPClient([
            'base_uri' => $this->params['api']['base_uri'],
            'timeout'  => 60.0
        ]);
    }

    /**
     * Get the ID of the Crawler instance's execution.
     * 
     * @return string|false $id ID string of the instance execution or false if not created.
     * @access public
     */
    public function get_id() {
        // Create a call.
        $req  = $this->_req( 'POST', $this->params['api']['call'], 0 );
        if ( $this->_code( $req ) == '201' ) {
            $res  = $this->_res($req);
            $id   = $res['_id'];
            return $id;
        } else {
            return false;
        }

    }

    /**
     * Get the results of the execution.
     *
     * @param  string $id ID String of the Crawler instance's execution.
     * @param  float $delay Number of miliseconds delay for the request
     * @return array|false $results|fase Array of Crawler's PageFunction results or false if error.
     * @access public
     */
    public function get_results( $id ) {
        // Get the results.
        $api_call = '/v1/execs/' . $id .'/results';
        $req      = $this->_req( 'GET', $api_call, 10000 );
        if ( $this->_code( $req ) == '200' ) {
            $res      = $this->_res( $req );
            $results  = $res[0]['pageFunctionResult'];
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Create a request and get the response object.
     * 
     * @param  string $method Method of the request.
     * @param  string $api_call API Call string.
     * @return object $req Result of the request.
     * @access private
     */
    private function _req( $method, $call, $delay ) {
        $req = $this->client->request( $method, $call, ['delay' => $delay]);
        return $req;
    }

    /**
     * Get the status code of the request.
     * 
     * @param  object $req Response object.
     * @return string $code Code of the status, 201 for Created (POST) and 200 for OK (GET)
     */
    private function _code( $req ) {
        $code = $req->getStatusCode();
        return $code;
    }

    /**
     * Decode the JSON Response into PHP's Array.
     * 
     * @param  object $req Response object (JSON).
     * @return array $res Array of the response object.
     * @access private
     */
    private function _res( $req ) {
        $res = json_decode( $req-> getBody(), true );
        return $res;
    }
}
