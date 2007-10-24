<?php

/**
 * Used to get Incoming Links from th Technorati Cosmos query.
 	Uses 
 	- To generate Incoming Links report in CMS Admin (like in wordpress, or even better)
 	- Have a list of trackbacks for each blog post.
 	- Have a stingy blogroll by linking only to the people who have linked to you ;)
 */
class CosmoService extends RestfulService {
	protected $api_key;
	
	function __construct(){
		$this->baseURL = 'http://api.technorati.com/cosmos';
	}
	
	function setApiKey($key){
		$this->api_key = $key;
	}
	
	function getApiKey(){
		return $this->api_key;
	}
	
	/**
 	* Gets the list of incoming links for particular URL as an array. 
 	* Checkout http://www.technorati.com/developers/api/cosmos.html to get an idea of response format.
 	* @params url - the url of the page to which has incoming links.
 	* @params type - A value of link returns the freshest links referencing your target URL.
    A value of weblog returns the last set of unique weblogs referencing your target URL.
 	* @params limit - Adjust the size of your result from the default value of 20 to between 1 and 100 results.
 	* @params start - Adjust the range of your result set. Set this number to larger than zero and you will receive the portion of Technorati's total result set ranging from start to start+limit. The default start value is 1.
 	*/
	
	function getIncomingLinks($url,  $type="", $limit=Null, $start=Null){
		$params = array(
			'url' => $url,
			'start' => $start,
			'limit' => $limit,
			'type' => $type,
			'key' => $this->getApiKey()
			);
		
		$this->setQueryString($params);
		$conn = $this->connect();
		$results = $this->getValues($conn, 'document', 'item');	
		
		return $results;
	}
	
	
		
}
?>