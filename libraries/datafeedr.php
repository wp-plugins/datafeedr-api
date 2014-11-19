<?php

/**
 * Datafeedr Api Client Library.
 *
 * @version 0.1b.6928
 * @copyright Datafeedr 2007 ~ 2014 - All Rights Reserved
 *
 * @mainpage
 *
 * Example of use:
 *
 * @code
 *		$api = new DatafeedrApi('<access id>', '<secret key>');
 *
 *		foreach($api->getMerchants() as $merchant)
 *			print $merchant['name'];
 *
 *		$search = $api->searchRequest();
 *		$search->addFilter("name LIKE shoe");
 *		$search->addFilter("price < 100");
 *		$search->addSort("price", DatafeedrApi::SORT_ASCENDING);
 *		$search->setLimit(25);
 *
 *		$products = $search->execute();
 *
 *		foreach($products as $product)
 *			print $product['name'];
 *
 * @endcode
 *
 *
 *
**/

if(!class_exists('DatafeedrApi', false)) {

/**
 * Datafeedr API core class.
 **/
class DatafeedrApi
{
    protected $_accessId;
    protected $_secretKey;

    protected $_transport;
    protected $_timeout;
    protected $_url;
    protected $_returnObjects;

    protected $_status;

    const SORT_DESCENDING = -1;
    const SORT_ASCENDING  = +1;

    const DEFAULT_URL = 'http://api.datafeedr.com';
    const DEFAULT_TIMEOUT = 30;

    const VERSION = '0.1b.6928';

    /**
     * Constructor.
     *
     * @param string          $accessId      Access ID.
     * @param object          $secretKey     Secret key.
     * @param string|callable $transport     (optional) HTTP transport function.
     * @param int             $timeout       (optional) HTTP connection timeout, in seconds.
     * @param bool            $returnObjects (optional) if TRUE, responses are objects, otherwise associative arrays.
     *
     * The optional $transport parameter tells how HTTP requests should be made.
     * It can be either a string that describes one of built-in transports ("curl", "file" or "socket"),
     * or a callable object that should accept an url, an array of headers and a string of post data and
     * should return an array [int http response status, string response body].
     *
    **/
    public function __construct($accessId, $secretKey, $transport = 'curl', $timeout = 0, $returnObjects = FALSE) {
        $this->_accessId = $accessId;
        $this->_secretKey = $secretKey;

        $this->_errors = array(
            1 => 'DatafeedrBadRequestError',
            2 => 'DatafeedrAuthenticationError',
            3 => 'DatafeedrLimitExceededError',
            4 => 'DatafeedrQueryError',
            7 => 'DatafeedrExternalError',
            9 => 'DatafeedrError',
        );

        $this->_url = self::DEFAULT_URL;
        $this->_timeout = $timeout ? $timeout : self::DEFAULT_TIMEOUT;
        $this->_returnObjects = $returnObjects;

        switch($transport) {
            case 'curl':
                $this->_transport = array($this, '_transportCurl');
                break;
            case 'file':
                $this->_transport = array($this, '_transportFile');
                break;
            case 'socket':
                $this->_transport = array($this, '_transportSocket');
                break;
            default:
                if(!is_callable($transport)) {
                    throw new DatafeedrError("Transport must be a function");
                }
                $this->_transport = $transport;
        }
    }

    /**
     * Return status information.
     *
     * @return array
     *
    **/
    public function getStatus() {
        $this->apiCall('status');
        return $this->_status;
    }

    /**
     * Return status information from the last request.
     *
     * If no Api request have been made, return NULL
     *
     * @return array|null
     *
    **/
    public function lastStatus() {
        return $this->_status;
    }

    /**
     * Return the list of networks.
     *
     * @param  int|array $networkId    (optional) Network id or an array of network ids
     * @param  bool      $includeEmpty (optional) If FALSE, omit networks with 0 products
     * @param  array     $fields       (optional) list of fields to retrieve
     * @return array
     *
    **/
    public function getNetworks($networkId = NULL, $includeEmpty = FALSE, $fields = NULL) {
        $request = array();
        if($networkId) {
            $request['_ids'] = $this->_intarray($networkId);
        }
        $request['skip_empty'] = intval(!$includeEmpty);
        if($fields) {
            $request['fields'] = $fields;
        }
        $response = $this->apiCall('networks', $request);
        return $this->_get($response, 'networks');
    }

    /**
     * Return the list of merchants.
     *
     * @param  int|array $networkId     (optional) Network id or array of network ids
     * @param  bool      $includeEmpty  (optional) If FALSE, omit merchants with 0 products
     * @param  array     $fields        (optional) list of fields to retrieve
     * @return array
     *
    **/
    public function getMerchants($networkId = NULL, $includeEmpty = FALSE, $fields = NULL) {
        $request = array();
        if($networkId) {
            $request['source_ids'] = $this->_intarray($networkId);
        }
        $request['skip_empty'] = intval(!$includeEmpty);
        if($fields) {
            $request['fields'] = $fields;
        }
        $response = $this->apiCall('merchants', $request);
        return $this->_get($response, 'merchants');
    }

    /**
     * Return the list of merchants by their ids.
     *
     * @param  int|array $merchantId    Merchant id or array of network ids
     * @param  bool      $includeEmpty  (optional) If FALSE, omit merchants with 0 products
     * @param  array     $fields        (optional) list of fields to retrieve
     * @return array
     *
    **/
    public function getMerchantsById($merchantId, $includeEmpty = FALSE, $fields = NULL) {
        $request = array();
        $request['_ids'] = $this->_intarray($merchantId);
        $request['skip_empty'] = intval(!$includeEmpty);
        if($fields) {
            $request['fields'] = $fields;
        }
        $response = $this->apiCall('merchants', $request);
        return $this->_get($response, 'merchants');
    }

    /**
     * Return the list of searchable fields.
     *
     * @param  int|array $networkId (optional) Network id or array of network ids
     * @return array
     *
    **/
    public function getFields($networkId = NULL) {
        $request = array();
        if($networkId) {
            $request['source_ids'] = $this->_intarray($networkId);
        }
        $response = $this->apiCall('fields', $request);
        return $this->_get($response, 'fields');
    }

    /**
     * Return the list of products by their ids.
     *
     * @param  int|array  $productId  Product id or an array of products ids.
     * @param  array      $fields     (optional) list of fields to retrieve.
     * @return array
     *
    **/
    public function getProducts($productId, $fields = NULL) {
        $request = array();
        $request['_ids'] = $this->_intarray($productId);
        $request['string_ids'] = 1;
        if($fields) {
            $request['fields'] = $fields;
        }
        $response = $this->apiCall('get', $request);
        return $this->_get($response, 'products');
    }

    /**
     * Return the list of Zanox merchant ids ("zmids").
     *
     * @param  int|array  $merchantId  Merchant id or an array of merchant ids.
     * @param  int        $adspaceId   Zanox adspace Id.
     * @param  string     $connectId   Zanox connection Id.
     * @return array
     *
    **/
    public function getZanoxMerchantIds($merchantId, $adspaceId, $connectId) {
        $request = array();
        $request['merchant_ids'] = $this->_intarray($merchantId);
        $request['adspace_id'] = $adspaceId;
        $request['connect_id'] = $connectId;
        $response = $this->apiCall('zanox_merchant_ids', $request);
        return $this->_get($response, 'zanox_merchant_ids');
    }

    /**
     * Create a new DatafeedrSearchRequest object.
     *
     * @return DatafeedrSearchRequest
     *
    **/
    public function searchRequest() {
        return new DatafeedrSearchRequest($this);
    }

    /**
     * Create a new DatafeedrMerchantSearchRequest object.
     *
     * @return DatafeedrMerchantSearchRequest
     *
    **/
    public function merchantSearchRequest() {
        return new DatafeedrMerchantSearchRequest($this);
    }

    /**
     * Create a new DatafeedrAmazonSearchRequest object.
     *
     * @return DatafeedrAmazonSearchRequest
     *
    **/
    public function amazonSearchRequest($awsAccessKeyId,  $awsSecretKey, $awsAssociateTag, $locale="US") {
        return new DatafeedrAmazonSearchRequest($this, $awsAccessKeyId,  $awsSecretKey, $awsAssociateTag, $locale);
    }

    /**
     * Create a new DatafeedrAmazonLookupRequest object.
     *
     * @return DatafeedrAmazonLookupRequest
     *
     **/
    public function amazonLookupRequest($awsAccessKeyId,  $awsSecretKey, $awsAssociateTag, $locale="US") {
        return new DatafeedrAmazonLookupRequest($this, $awsAccessKeyId,  $awsSecretKey, $awsAssociateTag, $locale);
    }

    /**
     * Perform the raw Api call.
     *
     * @param  string  $action  Api action.
     * @param  array   $request (optional) Request data.
     * @return array
     *
    **/
    public function apiCall($action, $request = NULL) {
        if(!$request) {
            $request = array();
        }

        $request['aid'] = $this->_accessId;
        $request['timestamp'] = gmdate('Y-m-d H:i:s');

        $message = $request['aid'] .$action . $request['timestamp'];
        $request['signature'] = hash_hmac('sha256', $message, $this->_secretKey, FALSE);

        $postdata = json_encode($request);
        $url = $this->_url . '/' . $action;
        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Content-Length: '. strlen($postdata),
            'Connection: close',
            'User-Agent: datafeedr.php.' . self::VERSION
        );
        list($status, $response) = call_user_func($this->_transport, $url, $headers, $postdata);
        if(strlen($response)) {
            $response = json_decode($response, !$this->_returnObjects);
        }

        $error = $this->_get($response, 'error');
        if($error) {
            $type = $this->_get($response, 'type');
            $cls  = isset($this->_errors[$type]) ? $this->_errors[$type] : 'DatafeedrError';
            throw new $cls($this->_get($response, 'message'), $error);
        }

        if($status != 200) {
            throw new DatafeedrHTTPError("Status $status");
        }

        $this->_status = $this->_get($response, 'status');
        return $response;
    }

    protected function _intarray($id_or_ids) {
        if(is_numeric($id_or_ids)) {
            return array($id_or_ids);
        }
        if(is_array($id_or_ids)) {
            return $id_or_ids;
        }
        return array();
    }

    protected function _get($obj, $prop, $default=NULL) {
        if(is_array($obj) && isset($obj[$prop])) {
            return $obj[$prop];
        }
        if(is_object($obj) && isset($obj->$prop)) {
            return $obj->$prop;
        }
        return $default;
    }


    /**
     * Perform a HTTP post request by the means of the curl library.
     *
     * @param  string  $url       Request url.
     * @param  array   $headers   Array of headers.
     * @param  string  $postdata  Post data.
     * @return array              (int http status, string response body)
     *
    **/
    protected function _transportCurl($url, $headers, $postdata) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->_timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);

        $response = curl_exec($ch);
        $status   = intval(curl_getinfo($ch, CURLINFO_HTTP_CODE));
        $errno    = curl_errno($ch);
        $errmsg   = curl_error($ch);

        curl_close($ch);

        if($errno) {
            throw new DatafeedrHTTPError($errmsg, $errno);
        }

        return array($status, $response);
    }

    /**
     * Perform a HTTP post request using file functions.
     *
     * @param  string  $url       Request url.
     * @param  array   $headers   Array of headers.
     * @param  string  $postdata  Post data.
     * @return array              (int http status, string response body)
     *
    **/
    protected function _transportFile($url, $headers, $postdata) {
        $options = array('http' => array(
            'method'  => 'POST',
            'content' => $postdata,
            'header'  => implode("\r\n", $headers),
            'ignore_errors' => TRUE,
            'timeout' => $this->_timeout,
        ));
        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        $status = 200;
        if(isset($http_response_header) && isset($http_response_header[0])) {
            if(preg_match('/HTTP.+?(\d\d\d)/', $http_response_header[0], $match)) {
                $status = intval($match[1]);
            }
        } else if($response === false) {
            throw new DatafeedrHTTPError("HTTP error: invalid response");
        }
        return array($status, $response);
    }

    /**
     * Perform a HTTP post request using sockets.
     *
     * @param  string  $url       Request url.
     * @param  array   $headers   Array of headers.
     * @param  string  $postdata  Post data.
     * @return array              (int http status, string response body)
     *
    **/
    protected function _transportSocket($url, $headers, $postdata) {
        $parts = parse_url($url);
        $errno  = 0;
        $errmsg = '';

        $fp = fsockopen($parts['host'], 80, $errno, $errmsg, $this->_timeout);
        if(!$fp) {
            throw new DatafeedrHTTPError($errmsg, $errno);
        }

        fwrite($fp, "POST " . $parts['path'] . " HTTP/1.1\r\n");
        fwrite($fp, implode("\r\n", $headers) . "\r\n\r\n");
        fwrite($fp, $postdata);

        $buf = '';
        while(!feof($fp)) {
            $buf .= fgets($fp, 1024);
        }
        fclose($fp);

        $buf = explode("\r\n\r\n", $buf, 2);
        if(count($buf) != 2) {
            throw new DatafeedrHTTPError("Invalid response");
        }
        if(preg_match('/HTTP.+?(\d\d\d)/', $buf[0], $match)) {
            $status = intval($match[1]);
        } else {
            throw new DatafeedrHTTPError("Invalid status");
        }
        return array($status, $buf[1]);
    }
}
}

if(!class_exists('DatafeedrSearchRequestBase', false)) {
/**
 * Generic Datafeedr API search request.
 **/
class DatafeedrSearchRequestBase
{
    protected $_api;
    protected $_lastResponse;

    /**
     * Constructor.
     *
     * @param object $api DatafeedrApi object.
     *
     **/
    public function __construct($api) {
        $this->_api = $api;
    }

    /**
     * Get the number of found products.
     *
     * @return int
     *
     **/
    public function getFoundCount() {
        return $this->_responseItem('found_count', 0);
    }

    /**
     * Get the number of products that can be retrieved from the server.
     *
     * @return int
     *
     **/
    public function getResultCount() {
        return $this->_responseItem('result_count', 0);
    }

    /**
     * Return the response from the last search.
     *
     * @return array
     *
     **/
    public function getResponse() {
        return $this->_lastResponse;
    }

    protected function _responseItem($prop, $default) {
        if(is_null($this->_lastResponse)) {
            throw new DatafeedrError("Reading from an empty request");
        }
        if(is_object($this->_lastResponse) && isset($this->_lastResponse->$prop)) {
            return $this->_lastResponse->$prop;
        }
        if(is_array($this->_lastResponse) && isset($this->_lastResponse[$prop])) {
            return $this->_lastResponse[$prop];
        }
        return $default;
    }

    function _apiCall($action, $request = NULL) {
        $this->_lastResponse = $this->_api->apiCall($action, $request);
    }

}
}

if(!class_exists('DatafeedrSearchRequest', false)) {
/**
 * Search request for Datafeedr API.
**/
class DatafeedrSearchRequest extends DatafeedrSearchRequestBase
{
    /**
     * Constructor.
     *
     * @param object $api DatafeedrApi object.
     *
    **/
    public function __construct($api) {
        parent::__construct($api);

        $this->_query       = array();
        $this->_sort        = array();
        $this->_fields      = array();
        $this->_limit       = 0;
        $this->_offset      = 0;
        $this->_priceGroups = 0;
        $this->_excludeDuplicates = "";
    }

    /**
     * Add a query filter.
     *
     * @param  string $filter Query filter.
     * @return $this
     *
    **/
    public function addFilter($filter) {
        $this->_query []= $filter;
        return $this;
    }

    /**
     * Add a sort field.
     *
     * @param  string $field   Field name.
     * @param  int    $order   One of DatafeedrApi::SORT_ASCENDING or DatafeedrApi::SORT_DESCENDING
     * @return $this
     *
    **/
    public function addSort($field, $order = DatafeedrApi::SORT_ASCENDING) {
        if(strlen($field) && ($field[0] == '+' || $field[0] == '-')) {
            $this->_sort []= $field;
        } else if($order == DatafeedrApi::SORT_ASCENDING) {
            $this->_sort []= '+' . $field;
        } else if($order == DatafeedrApi::SORT_DESCENDING) {
            $this->_sort []= '-' . $field;
        } else {
            throw new DatafeedrError("Invalid sort order");
        }
        return $this;
    }

    /**
     * Set which fields to retrieve.
     *
     * @param  array $fields List of field names.
     * @return $this
     *
    **/
    public function setFields($fields) {
        $this->_fields = $fields;
        return $this;
    }

    /**
     * Exclude duplicate results.
     *
     * @param  string $filter Equality filter in form "field1 field2 | field3".
     * @return $this
     *
    **/
    public function excludeDuplicates($filter) {
        if(is_array($filter)) {
            $filter = implode(' ', $filter);
        }
        $this->_excludeDuplicates = $filter;
        return $this;
    }

    /**
     * Set a limit.
     *
     * @param  int $limit The limit.
     * @return $this
     *
    **/
    public function setLimit($limit) {
        $this->_limit = $limit;
        return $this;
    }

    /**
     * Set an offset.
     *
     * @param  int $offset The offset.
     * @return $this
     *
    **/
    public function setOffset($offset) {
        $this->_offset = $offset;
        return $this;
    }

    /**
     * Set a price group count.
     *
     * @param  int $groups Group count.
     * @return $this
     *
    **/
    public function setPriceGroups($groups) {
        $this->_priceGroups = $groups;
        return $this;
    }

    /**
     * Get found networks.
     *
     * @return array
     *
    **/
    public function getNetworks() {
        return $this->_responseItem('networks', array());
    }

    /**
     * Get found merchants.
     *
     * @return array
     *
    **/
    public function getMerchants() {
        return $this->_responseItem('merchants', array());
    }

    /**
     * Get found price groups.
     *
     * @return array
     *
    **/
    public function getPriceGroups() {
        return $this->_responseItem('price_groups', array());
    }

    /**
     * Create a request object to use with the API.
     *
     * @return array
     *
    **/
    public function getParams() {
        $request = array();
        if($this->_query) {
            $request['query'] = $this->_query;
        }
        if($this->_sort) {
            $request['sort'] = $this->_sort;
        }
        if($this->_fields) {
            $request['fields'] = $this->_fields;
        }
        if($this->_limit) {
            $request['limit'] = $this->_limit;
        }
        if($this->_offset) {
            $request['offset'] = $this->_offset;
        }
        if($this->_priceGroups) {
            $request['price_groups'] = $this->_priceGroups;
        }
        if($this->_excludeDuplicates) {
            $request['exclude_duplicates'] = $this->_excludeDuplicates;
        }
        $request['string_ids'] = 1;
        return $request;
    }

    /**
     * Run search and return a list of products.
     *
     * @return array
     *
    **/
    public function execute() {
        $params = $this->getParams();
        if(!isset($params['query'])) {
            throw new DatafeedrError("Query can't be empty");
        }
        $this->_apiCall('search', $params);
        return $this->_responseItem('products', array());
    }

}
}

if(!class_exists('DatafeedrMerchantSearchRequest', false)) {
/**
 * Search request for Datafeedr Merchants.
 **/
class DatafeedrMerchantSearchRequest extends DatafeedrSearchRequestBase
{
    /**
     * Constructor.
     *
     * @param object $api DatafeedrApi object.
     *
     **/
    public function __construct($api) {
        parent::__construct($api);

        $this->_query       = array();
        $this->_sort        = array();
        $this->_fields      = array();
        $this->_limit       = 0;
        $this->_offset      = 0;
    }

    /**
     * Add a query filter.
     *
     * @param  string $filter Query filter.
     * @return $this
     *
     **/
    public function addFilter($filter) {
        $this->_query []= $filter;
        return $this;
    }

    /**
     * Add a sort field.
     *
     * @param  string $field   Field name.
     * @param int $order One of DatafeedrApi::SORT_ASCENDING or DatafeedrApi::SORT_DESCENDING
     * @throws DatafeedrError
     * @return $this
     *
     **/
    public function addSort($field, $order = DatafeedrApi::SORT_ASCENDING) {
        if(strlen($field) && ($field[0] == '+' || $field[0] == '-')) {
            $this->_sort []= $field;
        } else if($order == DatafeedrApi::SORT_ASCENDING) {
            $this->_sort []= '+' . $field;
        } else if($order == DatafeedrApi::SORT_DESCENDING) {
            $this->_sort []= '-' . $field;
        } else {
            throw new DatafeedrError("Invalid sort order");
        }
        return $this;
    }

    /**
     * Set which fields to retrieve.
     *
     * @param  array $fields List of field names.
     * @return $this
     *
     **/
    public function setFields($fields) {
        $this->_fields = $fields;
        return $this;
    }

    /**
     * Set a limit.
     *
     * @param  int $limit The limit.
     * @return $this
     *
     **/
    public function setLimit($limit) {
        $this->_limit = $limit;
        return $this;
    }

    /**
     * Set an offset.
     *
     * @param  int $offset The offset.
     * @return $this
     *
     **/
    public function setOffset($offset) {
        $this->_offset = $offset;
        return $this;
    }

    /**
     * Get found networks.
     *
     * @return array
     *
     **/
    public function getNetworks() {
        return $this->_responseItem('networks', array());
    }

    /**
     * Get found merchants.
     *
     * @return array
     *
     **/
    public function getMerchants() {
        return $this->_responseItem('merchants', array());
    }

    /**
     * Run search and return a list of merchants.
     *
     * @throws DatafeedrError
     * @return array
     *
     **/
    public function execute() {
        $params = $this->getParams();
        if(!isset($params['query'])) {
            throw new DatafeedrError("Query can't be empty");
        }
        $this->_apiCall('merchant_search', $params);
        return $this->_responseItem('merchants', array());
    }

    /**
     * Create a request object to use with the API.
     *
     * @return array
     *
     **/
    public function getParams() {
        $request = array();
        if($this->_query) {
            $request['query'] = $this->_query;
        }
        if($this->_sort) {
            $request['sort'] = $this->_sort;
        }
        if($this->_fields) {
            $request['fields'] = $this->_fields;
        }
        if($this->_limit) {
            $request['limit'] = $this->_limit;
        }
        if($this->_offset) {
            $request['offset'] = $this->_offset;
        }
        return $request;
    }
}
}

if(!class_exists('DatafeedrAmazonRequest', false)) {
/**
 * Generic Amazon request.
 **/
class DatafeedrAmazonRequest extends DatafeedrSearchRequestBase
{
    protected $_found = -1;

    const AWS_VERSION = "2011-08-01";

    /**
     * Constructor
     *
     * @param object $api              DatafeedrApi object.
     * @param string $awsAccessKeyId   Amazon access key.
     * @param string $awsSecretKey     Amazon secret key.
     * @param string $awsAssociateTag  Amazon associate tag.
     * @param string $locale           Amazon locale (two-letter code).
     */
    public function __construct($api, $awsAccessKeyId,  $awsSecretKey, $awsAssociateTag, $locale="US") {
        parent::__construct($api);
        $this->_hosts = array(
            "CA" => "webservices.amazon.ca",
            "CN" => "webservices.amazon.cn",
            "DE" => "webservices.amazon.de",
            "ES" => "webservices.amazon.es",
            "FR" => "webservices.amazon.fr",
            "IT" => "webservices.amazon.it",
            "JP" => "webservices.amazon.co.jp",
            "UK" => "webservices.amazon.co.uk",
            "US" => "webservices.amazon.com",
        );

        $this->_params = array();
        $this->_locale = strtoupper($locale);

        if(!isset($this->_hosts[$this->_locale])) {
            throw new DatafeedrError("Invalid Amazon locale");
        }

        $this->_awsAccessKeyId  =  $awsAccessKeyId;
        $this->_awsSecretKey    =  $awsSecretKey;
        $this->_awsAssociateTag =  $awsAssociateTag;
    }

    /**
     * Returns all parameters.
     *
     * @return array
     *
     **/
    public function getParams() {
        return $this->_params;
    }

    protected function _amazonUrl($operation, $params, $defaults=NULL) {
        $params = array_filter($params);

        if(!is_null($defaults)) {
            foreach($defaults as $k => $v) {
                if(!isset($params[$k])) {
                    $params[$k] = $v;
                }
            }
        }

        $params["Operation"]      = $operation;
        $params["Service"]        = "AWSECommerceService";
        $params["AWSAccessKeyId"] = $this->_awsAccessKeyId;
        $params["AssociateTag"]   = $this->_awsAssociateTag;
        $params["Version"]        = self::AWS_VERSION;
        $params["Timestamp"]      = gmdate("Y-m-d\\TH:i:s\\Z");

        ksort($params);
        $query = array();
        foreach($params as $k => $v) {
            if(is_array($v)) {
                $v = implode(',', $v);
            }
            $query []= $k . '=' . rawurlencode($v);
        }
        $query = implode('&', $query);
        $host = $this->_hosts[$this->_locale];
        $path = "/onca/xml";
        $subj = sprintf("GET\n%s\n%s\n%s", $host, $path, $query);
        $sign = rawurlencode(base64_encode(hash_hmac("sha256", $subj, $this->_awsSecretKey, TRUE)));
        return "http://{$host}{$path}?{$query}&Signature={$sign}";
    }
}
}

if(!class_exists('DatafeedrAmazonSearchRequest', false)) {
/**
 * Amazon search request.
 **/
class DatafeedrAmazonSearchRequest extends DatafeedrAmazonRequest
{
    /**
     * Add a parameter.
     *
     * @param  string $name  Parameter name.
     * @param  string $value Parameter value.
     * @return $this
     *
     * @see http://docs.aws.amazon.com/AWSECommerceService/latest/DG/ItemSearch.html
     *
     **/
    public function addParam($name, $value) {
        $this->_params[$name] = $value;
        return $this;
    }

    /**
     * Run search and return a list of products.
     *
     * @return array
     *
     **/
    public function execute() {
        $defaults = array(
            'ResponseGroup' => 'ItemAttributes,Images,OfferFull,BrowseNodes,EditorialReview,VariationSummary',
            'SearchIndex'   => 'All',
        );
        $url = $this->_amazonUrl('ItemSearch', $this->_params, $defaults);
        $this->_apiCall('amazon_search', array('url' => $url));
        return $this->_responseItem('products', array());
    }
}
}

if(!class_exists('DatafeedrAmazonLookupRequest', false)) {
/**
 * Amazon lookup request.
 **/
class DatafeedrAmazonLookupRequest extends DatafeedrAmazonRequest
{
    /**
     * Add a parameter.
     *
     * @param  string $name        Parameter name - one of 'ASIN', 'SKU', 'UPC', 'EAN', 'ISBN'
     * @param  string|array $value Parameter value or an array of values (up to 10).
     * @return $this
     *
     * @see http://docs.aws.amazon.com/AWSECommerceService/latest/DG/ItemLookup.html
     *
     **/
    public function addParam($name, $value) {
        $this->_params[$name] = $value;
        return $this;
    }

    /**
     * Run search and return a list of products.
     *
     * @return array
     *
     **/
    public function execute() {
        $params = array_filter($this->_params);
        $types = array('ASIN', 'SKU', 'UPC', 'EAN', 'ISBN', 'asin', 'sku', 'upc', 'ean', 'isbn');
        foreach($types as $type) {
            if(isset($params[$type])) {
                $params['IdType'] = strtoupper($type);
                $params['ItemId'] = $params[$type];
                unset($params[$type]);
            }
        }

        $defaults = array(
            'ResponseGroup' => 'ItemAttributes,Images,OfferFull,BrowseNodes,EditorialReview,VariationSummary',
        );
        if(isset($params['IdType']) && $params['IdType'] != 'ASIN') {
            $defaults['SearchIndex'] = 'All';
        }
        $url = $this->_amazonUrl('ItemLookup', $params, $defaults);
        $this->_apiCall('amazon_search', array('url' => $url));
        return $this->_responseItem('products', array());
    }
}
}

if(!class_exists('DatafeedrError', false)) {
/**
 * Generic Api error.
**/
class DatafeedrError extends Exception
{
}
}

if(!class_exists('DatafeedrBadRequestError', false)) {
/**
 * API error: Invalid Request.
**/
class DatafeedrBadRequestError extends DatafeedrError
{
}
}

if(!class_exists('DatafeedrAuthenticationError', false)) {
/**
 * API error: Authentication failed.
**/
class DatafeedrAuthenticationError extends DatafeedrError
{
}
}

if(!class_exists('DatafeedrLimitExceededError', false)) {
/**
 * API error: Query limit exceeded.
**/
class DatafeedrLimitExceededError extends DatafeedrError
{
}
}

if(!class_exists('DatafeedrHTTPError', false)) {
/**
 * API error: Unspecified HTTP error.
 **/
class DatafeedrHTTPError extends DatafeedrError
{
}
}

if(!class_exists('DatafeedrQueryError', false)) {
/**
 * API error: Error in the search query.
**/
class DatafeedrQueryError extends DatafeedrError
{
}
}

if(!class_exists('DatafeedrExternalError', false)) {
/**
 * API error: External service error.
**/
class DatafeedrExternalError extends DatafeedrError
{
}
}

?>