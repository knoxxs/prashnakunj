<?php

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
}
?>