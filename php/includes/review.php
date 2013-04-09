<?php
require_once __DIR__.'/user.php';
require_once __DIR__.'/base.php';
require_once __DIR__.'/base.php';

class Review extends Base{
	protected $QID, $suggestionUserName, $suggestionTimeStamp, $tagList;

	public function __construct($QID, $suggestionUserName=null, $suggestionTimeStamp=null){
		parent::__construct();
		$this->QID = $QID;
		$this->suggestionUserName = $suggestionUserName;
		$this->suggestionTimeStamp = $suggestionTimeStamp;

		$db = $this->getDb();
		$tagList = array();
		$db->query("SELECT tagName FROM Encompass WHERE QID='$QID'");
		$records = $db->fetch_assoc_all();
		foreach ($records as $key => $value){
			array_push($tagList, $value['tagName']);
		}

		$this->tagList = $tagList;
	}
	
	public function lockReview(){
		$db = $this->getDb();
		if(is_null($this->suggestionUserName)){
			$db->query("SELECT locked FROM Question WHERE QID='$this->QID'");
		}else{
			$db->query("SELECT locked FROM Suggestion WHERE QID='$this->QID' AND userName='$this->suggestionUserName' AND timeStamp=$this->suggestionTimeStamp");
		}
		$records = $db->fetch_assoc_all();
		$this->result['head']['status'] = 200;
		if(!$records[0]['locked']){
			if(is_null($this->suggestionUserName)){
				$db->query("SELECT locked FROM Question WHERE QID='$this->QID' AND reviewer=?",array(unserialize($_SESSION['user'])->getUserName()));
			}else{
				$db->query("SELECT locked FROM Suggestion WHERE QID='$this->QID' AND reviewerId=? AND userName='$this->suggestionUserName' AND timeStamp=$this->suggestionTimeStamp", array(unserialize($_SESSION['user'])->getUserName()));
			}
			$records = $db->fetch_assoc_all();
			if($db->returned_rows == 0){
				if(is_null($this->suggestionUserName)){
					return $db->query("UPDATE Question SET locked=1,reviewer=? WHERE QID='$this->QID'", array(unserialize($_SESSION['user'])->getUserName()));
				}else{
					return $db->query("UPDATE Question SET locked=1,reviewerId=? WHERE QID='$this->QID' AND userName='$this->suggestionUserName' AND timeStamp=$this->suggestionTimeStamp", array(unserialize($_SESSION['user'])->getUserName()));
				}
			}else{
				$this->result['head']['status'] = 403;
				$this->result['head']['message'] = "Already Locked";

			}
		}else{
			$this->result['head']['status'] = 409;
			$this->result['head']['message'] = "Already Locked";
		}
	}

	public function unlockReview(){
		$db = $this->getDb();
		if(is_null($this->suggestionUserName)){
			$db->query("SELECT locked,reviewer FROM Question WHERE QID='$this->QID'");
		}else{
			$db->query("SELECT locked,reviewerId as reviewer FROM Suggestion WHERE QID='$this->QID' AND userName='$this->suggestionUserName' AND timeStamp=$this->suggestionTimeStamp");
		}
		$records = $db->fetch_assoc_all();
		if($records[0]['locked'] && $records[0]['reviewer']){
			$this->result['head']['status'] = 200;
			if(is_null($this->suggestionUserName)){
				return $db->query("UPDATE Question SET locked=0,reviewer=NULL WHERE QID='$this->QID'");
			}else{
				return $db->query("UPDATE Question SET locked=0,reviewerId=NULL WHERE QID='$this->QID' AND userName='$this->suggestionUserName' AND timeStamp=$this->suggestionTimeStamp");
			}
		}else{
			$this->result['head']['status'] = 405;
			$this->result['head']['message'] = "Locked by someone else.";
		}
	}

	public function toArray(){
		$object = array();
		$object['QID'] = $this->QID;
		$object['suggestionUserName'] = $this->suggestionUserName;
		$object['suggestionTimeStamp'] = $this->suggestionTimeStamp;
		$object['tags'] = $this->tagList;
		return $object;
	}
}

//TODO : make tables for reviewer first, Now reviwer has different tags from accepts table (change lists accordingly), make tag table(no parent), comprehand shouldnot be a table