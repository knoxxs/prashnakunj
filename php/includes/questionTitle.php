<?php

require_once __DIR__.'/questionComment.php';
require_once __DIR__.'/base.php';
require_once __DIR__.'/reviewer.php';

class QuestionTitle extends Base
{
	private $QID, $string, $timeStamp, $difficultyLevel, $userName, $reviewer, $voteUp, $voteDown, $alreadyVoted, $alreadyFav, $tagList, $commentList, $requestedUser;
	public function __construct($QID, $string, $timeStamp, $difficultyLevel, $userName, $reviewer){
		parent::__construct();
		$this->QID = $QID;
		$this->userName = $userName;
		$this->string = $string;
		$this->timeStamp = $timeStamp;
		$this->difficultyLevel = $difficultyLevel;
		$this->reviewer = $reviewer;
		if($this->validateVar($_SESSION) && $this->validateVar($_SESSION['user'])){
			$this->requestedUser = unserialize($_SESSION['user'])->getUserName();
		}else{
			$this->requestedUser = null;
		}
		
		//fetching Votes
		$db = $this->getDb();
		$db->query("SELECT userName,nature FROM QuestionVotes WHERE QID=?",array($this->QID));
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

		//fetching Tags
		$db->query('SELECT tagName FROM Encompass WHERE QID=?',array($this->QID));
		$tagList = array();
		$records2 = $db->fetch_assoc_all();
		foreach ($records2 as $key2 => $value2) {
			array_push($tagList, $value2['tagName']);
		}
		$this->tagList = $tagList;

		//fetchingComment
		$db->query('SELECT userName,string,timeStamp FROM QuestionComment WHERE QID=? ORDER BY timeStamp DESC',array($this->QID));
		$commentList = array();
		$records = $db->fetch_assoc_all();
		foreach ($records as $key => $value) {
			array_push($commentList, new QuestionComment($this->QID, $value['userName'], $value['string'], $value['timeStamp']));
		}
		$this->commentList = $commentList;

		//fetching isAlredyFav
		$db->query('SELECT QID FROM Favourites WHERE QID=? AND userName=?',array($this->QID, $this->requestedUser));
		if($db->returned_rows == 1){
			$this->alreadyFav = true;
		}
		else
		{
			$this->alreadyFav = false;
		}
	}

	public function getQID()
	{
	    return $this->QID;
	}
		
	public function toArray(){
		$object = array();
		$object['QID'] = $this->QID;
		$object['userName'] = $this->userName;
		$object['string'] = $this->string;
		$object['timeStamp'] = $this->timeStamp;
		$object['voteUp'] = $this->voteUp;
		$object['voteDown'] = $this->voteDown;
		$object['difficultyLevel'] = $this->difficultyLevel;
		$object['alreadyVoted'] = $this->alreadyVoted;
		$object['alreadyFav'] = $this->alreadyFav;
		$object['tagList'] = $this->tagList;
		$commentsTemp = array();
		foreach ($this->commentList as $key => $value) {
			array_push($commentsTemp, $value->toArray());
		}
		$object['commentList'] = $commentsTemp;

		return ($object);
	}

}

?>