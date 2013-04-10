<?php

require_once __DIR__.'/base.php';

class AnswerComment extends Base
{
	private $QID, $userName, $string, $timeStamp, $voteUp, $voteDown, $alreadyVoted, $requestedUser, $reviewerID, $answerTimeStamp;

	public function __construct($QID, $reviewerID, $answerTimeStamp, $userName, $timeStamp, $string){
		parent::__construct();
		$this->QID = $QID;
		$this->reviewerID = $reviewerID;
		$this->answerTimeStamp = $answerTimeStamp;
		$this->userName = $userName;
		$this->string = $string;
		$this->timeStamp = $timeStamp;
		if($this->validateVar($_SESSION) && $this->validateVar($_SESSION['user'])){
			$this->requestedUser = unserialize($_SESSION['user'])->getUserName();
		}else{
			$this->requestedUser = null;
		}

		//fetching Votes
		$db = $this->getDb();
		$db->query("SELECT userName,nature FROM AnswerCommentVotes WHERE QID=? AND reviewerID=? AND answerTimeStamp=? AND answerCommentTimeStamp=? AND commentUserName=?",array($this->QID, $this->reviewerID, $this->answerTimeStamp, $this->timeStamp, $this->userName));
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
		$status = $db->query("INSERT INTO AnswerComment (QID, reviewerID, answerTimeStamp, userName, string) VALUES (?,?,?,?,?)", $data);
		return $status;
	}

	public function toArray(){
		$object = array();
		// $object['QID'] = $this->QID;
		$object['userName']=$this->userName;
		// $object['answerTimeStamp']=$this->answerTimeStamp;
		//$object['reviewerID'] = $this->reviewerID;
		$object['string'] = $this->string;
		$object['timeStamp']=$this->timeStamp;
		$object['voteUp'] = $this->voteUp;
		$object['voteDown'] = $this->voteDown;
		$object['alreadyVoted'] = $this->alreadyVoted;
		return ($object);
	}

}

?>