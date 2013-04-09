<?php

require_once __DIR__.'/base.php';

class SuggestionComment extends Base
{
	private $QID, $suggestionUserName, $suggestionTimeStamp, $userName, $string, $timeStamp, $voteUp, $voteDown, $alreadyVoted, $requestedUser;

	public function __construct($QID, $suggestionUserName, $suggestionTimeStamp, $string, $userName, $timeStamp){
		$this->QID = $QID;
		$this->suggestionUserName = $suggestionUserName;
		$this->suggestionTimeStamp = $suggestionTimeStamp;
		$this->userName = $userName;
		$this->string = $string;
		$this->timeStamp = $timeStamp;
		if($this->validateVar($_SESSION['user'])){
			$this->requestedUser = unserialize($_SESSION['user'])->getUserName();
		}else{
			$this->requestedUser = null;
		}

		//fetching Votes
		$db = $this->getDb();
		$db->query("SELECT userName,nature FROM SuggestionCommentVotes WHERE QID=? AND suggestionUserName=? AND suggestionTimeStamp=? AND suggestionCommentUserName=? AND suggestionCommentTimeStamp=?",array($this->QID, $this->suggestionUserName, $this->suggestionTimeStamp, $this->userName, $this->timeStamp));
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
	}

	public static function addComment(array $data){
		$db = (new Database())->connectToDatabase();
		$status = $db->query("INSERT INTO SuggestionComment (QID, suggestionUserName, suggestionTimeStamp, userName, string) VALUES (?,?,?,?,?)", $data);
		return $status;
	}

	public static function addVote(array $data)
	{
		$db = (new Database())->connectToDatabase();
		$status = $db->query("INSERT INTO SuggestionCommentVotes (QID, suggestionUserName, suggestionTimeStamp, suggestionCommentUserName, suggestionCommentTimeStamp, userName, nature) VALUES (?,?,?,?,?,?,?)", $data);
		return $status;
	}

	public static function checkAlreadyVoted(array $data)
	{
		$db = (new Database())->connectToDatabase();
		$records = $db->query("SELECT userName FROM SuggestionCommentVotes WHERE QID=? AND suggestionUserName=? AND suggestionTimeStamp=? AND suggestionCommentUserName=? AND suggestionCommentTimeStamp=? AND userName=?", $data);
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
		$db->query("SELECT nature FROM SuggestionCommentVotes WHERE QID=? AND suggestionUserName=? AND suggestionTimeStamp=? AND suggestionCommentUserName=? AND suggestionCommentTimeStamp=? AND userName=?", $data);
		$nature = $db->fetch_assoc_all()[0]['nature'];
		return $nature;
	}

	public static function updateVote($nature ,array $data)
	{
		$db = (new Database())->connectToDatabase();
		$status = $db->query("UPDATE SuggestionCommentVotes SET nature=$nature WHERE QID=? AND suggestionUserName=? AND suggestionTimeStamp=? AND suggestionCommentUserName=? AND suggestionCommentTimeStamp=? AND userName=?", $data);
		return $status;
	}

	public static function checkCommentUser(array $data)
	{
		$db = (new Database())->connectToDatabase();
		$uname = $db->query("SELECT userName FROM SuggestionComment WHERE QID=? AND suggestionUserName=? AND suggestionTimeStamp=? AND userName=? AND timeStamp=?", $data);
		return $uname;
	}

	public static function modifyComment($newString, array $data){
		$db = (new Database())->connectToDatabase();
		$status = $db->query("UPDATE SuggestionComment SET string=$newString WHERE QID=? AND suggestionUserName=? AND suggestionTimeStamp=? AND userName=? AND timeStamp=?", $data);
		return $status;	
	}

}
?>