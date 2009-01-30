<?php

class MetaBlog extends Page {
	static $db = array(
	"Tag" => "Varchar",
	"TopExcerptSize" => "Int",
	"ExcerptSize" => "Int",
	"PerPage" => "Int"
	);
	
	static $defaults = array(
		"TopExcerptSize" => 255,
		"ExcerptSize" => 100,
		"PerPage" => 10
	);
	
	static $icon = "metablogservice/images/technorati";
	
	 // add custom fields for metablog page 
	  function getCMSFields($cms) {
    	$fields = parent::getCMSFields($cms);
      	$fields->addFieldToTab("Root.Content.Posts", new TextField("Tag","Show posts tagged with :"));
      	$fields->addFieldToTab("Root.Content.Posts", new NumericField("ExcerptSize","Excerpt length", 100));
      	$fields->addFieldToTab("Root.Content.Posts", new NumericField("TopExcerptSize","Excerpt length of latest story", 255));
      	$fields->addFieldToTab("Root.Content.Posts", new NumericField("PerPage","Posts per page", 10));
      return $fields;
   }
   
   function BlogPosts(){
	$metablog = new MetablogService();
	$page = isset($_GET['page'])? (int)$_GET['page']: 1;
	
	$posts = $metablog->getPosts($this->Tag, $this->ExcerptSize, $this->TopExcerptSize, $this->PerPage, ($this->PerPage*($page-1))+1);
	
	if($posts){
		$postsHTML = "";
		foreach($posts as $post){
			$postsHTML .= "<div class='metablogPost'>";
			$postsHTML .= "<h2><a href='".$post->permalink."'>".$post->title."</a></h2>";
			$postsHTML .= "<div class='metablogPostInfo'>";
			$postsHTML .= "<span>".$post->created. " - </span><a href='".$post->weblog_url."'>".$post->weblog_name."</a>";
			$postsHTML .= "</div>";
			$postsHTML .= "<p>".$post->excerpt."...</p>";
			$postsHTML .= "</div>";	
		}
	
	
		$postsHTML .= "<div class='pages'><div class='paginator'>";
		$postsHTML .= $metablog->getPages();
		$postsHTML .= "</div><span class='results'>(".$metablog->getTotalPosts()." Posts)</span></div>";
	}
	else {
		$postsHTML .= "<span>Sorry!  No Posts Available.</span>";
	}
	
	//Debug::show($posts);
	return $postsHTML;
	}
   
}

class MetaBlog_Controller extends Page_Controller {

	function init() {
	  if(Director::fileExists(project() . "/css/MetaBlog.css")) {
         Requirements::css(project() . "/css/MetaBlog.css");
      }else{
         Requirements::css("metablogservice/css/MetaBlog.css");
      }
      
      parent::init();	
   }
   
   function Content(){
			return $this->Content.$this->BlogPosts();
   }
	
	
	
}

?>
