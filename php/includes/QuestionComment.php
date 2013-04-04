<?php

class QuestionComment extends Base
{
	private $QID, $userName, $string, $timeStamp, $voteUp, $voteDown, $alreadyVoted, $requestedUser;

	public function __construct($QID, $userName, $string, $timeStamp){
		$this->QID = $QID;
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
		$db->query("SELECT userName,nature FROM QuestionCommentVotes WHERE QID=? AND commentUserName=? AND commentTimeStamp=?",array($this->QID, $this->userName, $this->timeStamp));
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

	/*public static function addComment($QID, $userName, $string){
		$db = $this->getDb();
		$db->query("INSERT INTO QuestionComment (QID, userName, string) VALUES ('$QID', '$userName', '$string')");
		// return boolean for correct insert of comment. 
	}*/

	public static function addComment(array $data){
		$db = $this->getDb();
		$db->query("INSERT INTO Question (QID, userName, string) VALUES ('$data['QID']', '$data['userName']', '$data['Cstring']')");
		// return boolean for correct insert of comment. 
	}

}
?>