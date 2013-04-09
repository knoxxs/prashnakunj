<?php
require_once __DIR__.'/user.php';
require_once __DIR__.'/base.php';
require_once __DIR__.'/base.php';

class Review extends Base{
	protected $QID, $suggestionUserName, $suggestionTimeStamp, $tagList, $locker;

	public function __construct($QID, $suggestionUserName=null, $suggestionTimeStamp=null){
		parent::__construct();
		$this->QID = $QID;
		$this->suggestionUserName = $suggestionUserName;
		$this->suggestionTimeStamp = $suggestionTimeStamp;

		// $db = $this->getDb();
		// $tagList = array();
		// $db->query("SELECT name,parent FROM Comprehend WHERE QID='$this->QID' AND suggestionUserName='$this->suggestionUserName' AND suggestionTimeStamp=$this->suggestionTimeStamp");
		// $records = $db->fetch_assoc_all();
		// foreach ($records as $key => $value){
		// 	array_push($tagList, array("name" => $value['name'], "parent" => $value['name']));
		// }
		// $this->tagList = $tagList;
	}
	
	public function lockReview(){
		$db = $this->getDb();
		$db->query("SELECT locked FROM Review WHERE QID='$this->QID' AND suggestionUserName='$this->suggestionUserName' AND suggestionTimeStamp=$this->suggestionTimeStamp");
		$records = $db->fetch_assoc_all();
		if($records[0]['locked']){
			$this->locker = unserialize($_SESSION['user'])->getUserName();
			return $db->query("UPDATE Comprehend SET locked=True,reviewerId='$this->locker' WHERE QID='$this->QID' AND suggestionUserName='$this->suggestionUserName' AND suggestionTimeStamp=$this->suggestionTimeStamp");
		}else{
			return false;
		}
	}

	public function unlockReview(){
		$db = $this->getDb();
		return $db->query("UPDATE Comprehend SET locked=False,reviewerId=null WHERE QID='$this->QID' AND suggestionUserName='$this->suggestionUserName' AND suggestionTimeStamp=$this->suggestionTimeStamp");
	}

	public function toArray(){
		$object = array();
		$object['QID'] = $this->QID;
		$object['suggestionTimeStamp'] = $this->suggestionUserName;
		$object['suggestionTimeStamp'] = $this->suggestionTimeStamp;
	}
}

//TODO : make tables for reviewer first, Now reviwer has different tags from accepts table (change lists accordingly), make tag table(no parent), comprehand shouldnot be a table