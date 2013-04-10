<?php

require_once __DIR__.'/base.php';


class QuestionComment extends Base
{
	private $QID, $userName, $string, $timeStamp, $voteUp, $voteDown, $alreadyVoted, $requestedUser;

	public function __construct($QID, $userName, $string, $timeStamp){
		parent::__construct();
		$this->QID = $QID;
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

	public function toArray()
	{
		$object = array();
		// $object['QID'] = $this->QID;
		$object['userName']=$this->userName;
		$object['string'] = $this->string;
		$object['timeStamp']=$this->timeStamp;
		$object['voteUp'] = $this->voteUp;
		$object['voteDown'] = $this->voteDown;
		$object['alreadyVoted'] = $this->alreadyVoted;
		return ($object);

	}

	public static function addComment(array $data){
		$db = (new Database())->connectToDatabase();
		$status = $db->query("INSERT INTO QuestionComment (QID, userName, string) VALUES (?,?,?)", $data);
		return $status;
	}

	public static function addVote(array $data)
	{
		$db = (new Database())->connectToDatabase();
		$status = $db->query("INSERT INTO QuestionCommentVotes (userName, qid, nature, commentUserName, commentTimeStamp) VALUES (?,?,?,?,?)", $data);
		return $status;
	}

	public static function checkAlreadyVoted(array $data)
	{
		$db = (new Database())->connectToDatabase();
		$records = $db->query("SELECT userName FROM QuestionCommentVotes WHERE qid=? AND userName=? AND commentUserName=? AND commentTimeStamp=?", $data);
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
		$db->query("SELECT nature FROM QuestionCommentVotes WHERE qid=? AND userName=? AND commentUserName=? AND commentTimeStamp=?", $data);
		$nature = $db->fetch_assoc_all()[0]['nature'];
		return $nature;
	}

	public static function updateVote($nature ,array $data)
	{
		$db = (new Database())->connectToDatabase();
		$status = $db->query("UPDATE QuestionCommentVotes SET nature=$nature WHERE qid=? AND userName=? AND commentUserName=? AND commentTimeStamp=?", $data);
		return $status;
	}

	public static function checkCommentUser(array $data)
	{
		$db = (new Database())->connectToDatabase();
		$uname = $db->query("SELECT userName FROM QuestionComment WHERE QID=? AND userName=? AND timeStamp=?", $data);
		return $uname;
	}

	public static function modifyComment($newString, array $data){
		$db = (new Database())->connectToDatabase();
		$status = $db->query("UPDATE QuestionComment SET string=$newString WHERE QID=? AND userName=? AND timeStamp=? AND commentTimeStamp=?", $data);
		return $status;	
	}

}
?>