<?php
require_once __DIR__.'/user.php';

class Review extends Base{
	protected $QID, $suggestionUserName, $suggestionTimeStamp, $tagList;

	public function __construct($QID, $suggestionUserName=null, $suggestionTimeStamp=null){
		parent::__construct();
		$this->QID = $QID;
		$this->suggestionUserName = $suggestionUserName;
		$this->suggestionTimeStamp = $suggestionTimeStamp;

		$db = $this->getDb();
		$tagList = array();
		$db->query("SELECT name,parent FROM Comprehend WHERE QID='$this->QID' AND suggestionUserName='$this->suggestionUserName' AND suggestionTimeStamp=$this->suggestionTimeStamp");
		$records = $db->fetch_assoc_all();
		foreach ($records as $key => $value){
			array_push($tagList, array("name" => $value['name'], "parent" => $value['name']));
		}
		$this->tagList = $tagList;
	}
}