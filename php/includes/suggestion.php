<?php

require_once __DIR__.'/suggestionComment.php';
require_once __DIR__.'/base.php';
require_once __DIR__.'/reviewer.php';

class Suggestion extends Base
{
	private $QID, $userName, $timeStamp, $string, $used, $reviwerId, $requestedUser, $alreadyVoted, $commentList;

	public function __construct($QID, $userName, $timeStamp, $string, $used, $reviewerId){
		parent::__construct();
		$this->QID = $QID;
		$this->userName = $userName;
		$this->string = $string;
		$this->timeStamp = $timeStamp;
		$this->reviewerId = $reviewerId;
		$this->used = $used;
		if($this->validateVar($_SESSION) && $this->validateVar($_SESSION['user'])){
			$this->requestedUser = unserialize($_SESSION['user'])->getUserName();
		}else{
			$this->requestedUser = null;
		}

		//fetching Votes
		$db = $this->getDb();
		$db->query("SELECT userName,nature FROM SuggestionVotes WHERE QID=? AND suggestionUserName=? AND suggestionTimestamp=?",array($this->QID, $this->userName, $this->timeStamp));
		$alreadyVoted = 0;
		$voteUp = 0;
		$voteDown = 0;
		$records = $db->fetch_assoc_all();
		if(is_null($this->requestedUser)){
			foreach ($records as $key => $value) {
				if($value['nature'] > 0){
					$voteUp += 1;
				}else{
					$voteDown += 1;
				}
			}
		}else{
			foreach ($records as $key => $value) {
				if($value['nature'] > 0){
					$voteUp += 1;
				}else{
					$voteDown += 1;
				}
				if($value['userName'] == $this->requestedUser){
					$alreadyVoted = $value['nature'];
				}
			}
		}
		$this->voteUp = $voteUp;
		$this->voteDown = $voteDown;
		$this->alreadyVoted = $alreadyVoted;

		//fetchingComment
		$db->query("SELECT userName,string,timeStamp FROM SuggestionComment WHERE QID=? AND suggestionUserName=? AND suggestionTimestamp=? ORDER BY timeStamp DESC",array($this->QID, $this->userName, $this->timeStamp));
		$commentList = array();
		$records = $db->fetch_assoc_all();
		foreach ($records as $key => $value) {
			array_push($commentList, new SuggestionComment($this->QID, $this->userName, $this->timeStamp, $value['string'], $value['userName'], $value['timeStamp']));
		}
		$this->commentList = $commentList;
	}

	public static function addSuggestion($QID, $userName, $suggestionString)
	{
		$db = (new Database())->connectToDatabase();
		$status = $db->query("INSERT INTO Suggestion (QID, userName, string) VALUES ('$QID', '$userName', '$suggestionString')");
		return $status;
	}

	public static function addVote(array $data)
	{
		$db = (new Database())->connectToDatabase();
		$status = $db->query("INSERT INTO SuggestionVotes (QID, suggestionUserName, suggestionTimestamp, userName, nature) VALUES (?,?,?,?,?)", $data);
		return $status;
	}

	public static function checkAlreadyVoted(array $data)
	{
		$db = (new Database())->connectToDatabase();
		$records = $db->query("SELECT userName FROM SuggestionVotes WHERE QID=? AND suggestionUserName=? AND suggestionTimestamp=? AND userName=?", $data);
		if($db->returned_rows > 0){
			$name = $db->fetch_assoc_all()[0]['userName'];
		}
		else{
			$name = NULL;
		}	
		return $name;
	}

	public static function checkVoteNature(array $data)
	{
		$db = (new Database())->connectToDatabase();
		$db->query("SELECT nature FROM SuggestionVotes WHERE QID=? AND suggestionUserName=? AND suggestionTimestamp=? AND userName=?", $data);
		$nature = $db->fetch_assoc_all()[0]['nature'];
		return $nature;
	}

	public static function updateVote($nature, array $data)
	{
		$db = (new Database())->connectToDatabase();
		$status = $db->query("UPDATE SuggestionVotes SET nature=$nature WHERE QID=? AND suggestionUserName=? AND suggestionTimestamp=? AND userName=?", $data);
		return $status;
	}



	public static function compareVoteUp($a, $b){
		return $b->getVoteUp() - $a->getVoteUp();
	}

	public function toArray(){
		$object = array();
		// $object['QID'] = $this->QID;
		$object['userName'] = $this->userName;
		$object['string'] = $this->string;
		$object['timeStamp'] = $this->timeStamp;
		$object['voteUp'] = $this->voteUp;
		$object['voteDown'] = $this->voteDown;
		$object['alreadyVoted'] = $this->alreadyVoted;
		$object['used']=$this->used;
		$object['reviewerId']=$this->reviewerId;
		$commentsTemp = array();
		foreach ($this->commentList as $key => $value) {
			array_push($commentsTemp, $value->toArray());
		}
		$object['commentList'] = $commentsTemp;
		return $object;
	}

	public static function reviewSuggestion($QID, $userName, $timeStamp, $string){
		$db = (new Database())->connectToDatabase();
		$status = $db->query("UPDATE Suggestion SET string='$string', locked=0, reviewerId=? WHERE QID='$QID' AND userName='$userName' AND timeStamp='$timeStamp'", array(unserialize($_SESSION['user'])->getUserName() ));
		return $status;	
	}
}
?>