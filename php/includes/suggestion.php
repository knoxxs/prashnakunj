<?php

require_once __DIR__.'/suggestionComment.php';
require_once __DIR__.'/base.php';

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
		if($this->validateVar($_SESSION['user'])){
			$this->requestedUser = unserialize($_SESSION['user'])->getUserName();
		}else{
			$this->requestedUser = null;
		}

		//fetching Votes
		$db = $this->getDb();
		$db->query("SELECT userName,nature FROM SuggestionVotes WHERE QID=? AND suggestionUserName=? AND suggestionTimestamp=?",array($this->QID, $this->userName, $this->timestamp));
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
		$db->query("SELECT userName,string,timeStamp FROM SuggestionComment WHERE QID=? AND suggestionUserName=? AND suggestionTimestamp=? ORDER BY timeStamp DESC",array($this->QID, $this->userName, $this->timestamp));
		$commentList = array();
		$records = $db->fetch_assoc_all();
		foreach ($records as $key => $value) {
			array_push($commentList, new SuggestionComment($this->QID, $this->userName, $this->timestamp, $value['string'], $value['userName'], $value['timeStamp']));
		}
		$this->commentList = $commentList;
	}

	public static function compareVoteUp($a, $b){
		return $b->getVoteUp() - $a->getVoteUp();
	}

	public function getcommentlistarray()
		{
		$list = $this->commentList;
		$jsonList=array();
		foreach ($list as $key => $value) 
			{
			array_push($jsonList, $value->commentlist_to_array);
			}
		return $jsonList;
		}
		public function commentlist_to_array()
		{
			$object = array();
		$object['QID'] = $this->QID;
		$object['userName'] = $this->userName;
		$object['string'] = $this->string;
		$object['timeStamp'] = $this->timeStamp;
		return ($object);
		}

	public static function compareVoteUp($a, $b){
		return $b->getVoteUp() - $a->getVoteUp();
	}
	public function toArray(){
		$object = array();
		$object['QID'] = $this->QID;
		$object['userName'] = $this->userName;
		$object['string'] = $this->string;
		$object['timeStamp'] = $this->timeStamp;
		$object['voteUp'] = $this->voteUp;
		$object['voteDown'] = $this->voteDown;
		$object['alreadyVoted'] = $this->alreadyVoted;
		$object['commentList']=$this->getcommentlistarray();
		$object['used']=$this->used;
		$object['reviewerId']=$this->reviewerId;

		return ($object);
	}


}


?>