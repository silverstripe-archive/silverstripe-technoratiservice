<?php

/**
 * Used to create a meta blog using posts returned by Technorati for specific Tags
 */
class MetablogService extends RestfulService {
	private static $api_key;
	private $postCount;
	private $pageCount;
	
	function __construct($expiry=NULL){
		parent::__construct('http://www.flickr.com/services/rest/', $expiry);
	    $this->checkErrors = true;
	}
	
	/*
	This will return API specific error messages.
	*/
	function errorCatch($response){
		$err_msg = $this->searchValue($response, "//result/error");
	 if($err_msg)
		user_error("Technorati Service Error : $err_msg", E_USER_ERROR);
	 else
	 	return $response;
	}
	
	static function setApiKey($key){
		self::$api_key = $key;
	}
	
	function getApiKey(){
		return self::$api_key;
	}
	
	/**
 	* Gets the list of blog posts which is tagged with specified keyword as an array. 
 	* Checkout http://www.technorati.com/developers/api/tag.html to get an idea of response format.
 	* @params tag - the tag you like to find the posts.
 	* @params type - A value of link returns the freshest links referencing your target URL.
    A value of weblog returns the last set of unique weblogs referencing your target URL.
 	* @params limit - Adjust the size of your result from the default value of 20 to between 1 and 100 results.
 	* @params start - Adjust the range of your result set. Set this number to larger than zero and you will receive the portion of Technorati's total result set ranging from start to start+limit. The default start value is 1.
 	*/
	
	function getPosts($tag, $excerpt=100, $topexcerpt=150, $perpage=20, $start=Null){
		//Debug::show();
		
		$params = array(
			'tag' => $tag,
			'excerptsize' => "$excerpt",
			'topexcerptsize' => "$topexcerpt",
			'start' => "$start",
			'limit' => "$perpage",
			'key' => $this->getApiKey()
			);
		
		$this->setQueryString($params);
		$conn = $this->connect();
		
		$results = $this->getValues($conn, 'document', 'item');	
		$this->postCount = $this->searchValue($conn, '//result/postsmatched');
		$this->pageCount = (int)($this->postCount/$perpage);
		
		
		return $results;
	}
	
	function Paginate(){
	$current_url = Director::currentURLSegment();

		$current_page = isset($_GET['page'])? (int)$_GET['page']: 1;;
		$last_page = $this->pageCount;
		//$this->TotalPosts = $this->postCount;
		
		
		if($current_page > 1){
			$qs = http_build_query(array('page' => $current_page - 1));
			$pagelist = "<a href='$current_url?$qs' class='prev'>&lt; Previous</a>";
		}
		
		if($current_page < 6)
			$start = 0;
		else
			$start = $current_page - 5;
		
		$end = $last_page < 10 ? $last_page : $start+10;
		
		$pagelist = "";
		for($i=$start; $i < $end ; $i++){
			$pagenum = $i + 1;
			if($pagenum != $current_page){
				$qs = http_build_query(array('page' => $pagenum));
				$page_item = "<a href='$current_url?$qs'>$pagenum</a>";
			}
			else 
				$page_item = "<span class='currentPage'>$pagenum</span>";
				
			$pagelist .= $page_item;
		}
		
		if ($current_page < $last_page){
			$qs = http_build_query(array('page' => $current_page + 1));
			$pagelist .= "<a href='$current_url?$qs' class='next'>Next &gt;</a>";
		}
			
		
		//Debug::show($pagination);
		return $pagelist;
	}
	
	function getPages(){
		return $this->Paginate();
	}
	
	function getTotalPosts(){
		return $this->postCount;
	}
	
	
		
}
?>