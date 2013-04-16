<?php

require_once __DIR__.'/base.php';

class AnswerComment extends Base
{
	private $QID, $userName, $string, $timeStamp, $voteUp, $voteDown, $alreadyVoted, $requestedUser, $reviewerId, $answerTimeStamp;

	public function __construct($QID, $reviewerId, $answerTimeStamp, $userName, $timeStamp, $string){
		parent::__construct();
		$this->QID = $QID;
		$this->reviewerId = $reviewerId;
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
		$db->query("SELECT userName,nature FROM AnswerCommentVotes WHERE QID=? AND reviewerId=? AND answerTimeStamp=? AND answerCommentTimeStamp=? AND commentUserName=?",array($this->QID, $this->reviewerId, $this->answerTimeStamp, $this->timeStamp, $this->userName));
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
		$status = $db->query("INSERT INTO AnswerComment (QID, reviewerId, answerTimeStamp, string, userName) VALUES (?,?,?,?,?)", $data);
		return $status;
	}

	public function toArray(){
		$object = array();
		// $object['QID'] = $this->QID;
		$object['userName']=$this->userName;
		// $object['answerTimeStamp']=$this->answerTimeStamp;
		//$object['reviewerId'] = $this->reviewerId;
		$object['string'] = $this->string;
		$object['timeStamp']=$this->timeStamp;
		$object['voteUp'] = $this->voteUp;
		$object['voteDown'] = $this->voteDown;
		$object['alreadyVoted'] = $this->alreadyVoted;
		return ($object);
	}

	public static function addVote(array $data)
	{
		$db = (new Database())->connectToDatabase();
		$status = $db->query("INSERT INTO AnswerCommentVotes (QID, reviewerID, answerTimeStamp, answerCommentTimeStamp, commentUserName, userName, nature) VALUES (?,?,?,?,?,?,?)", $data);
		
		return $status;
	}

	public static function checkAlreadyVoted(array $data)
	{
		$db = (new Database())->connectToDatabase();
		$records = $db->query("SELECT userName FROM AnswerCommentVotes WHERE QID=? AND reviewerID=? AND answerTimeStamp=? AND answerCommentTimeStamp=? AND commentUserName=? AND userName=?", $data);
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
		$db->query("SELECT nature FROM AnswerCommentVotes WHERE QID=? AND reviewerID=? AND answerTimeStamp=? AND answerCommentTimeStamp=? AND commentUserName=? AND userName=?", $data);
		$nature = $db->fetch_assoc_all()[0]['nature'];
		return $nature;
	}

	public static function updateVote($nature ,array $data)
	{
		$db = (new Database())->connectToDatabase();
		$status = $db->query("UPDATE AnswerCommentVotes SET nature=$nature WHERE QID=? AND reviewerID=? AND answerTimeStamp=? AND answerCommentTimeStamp=? AND commentUserName=? AND userName=?", $data);
		return $status;
	}

}

?>