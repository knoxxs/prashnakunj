<?php

require_once __DIR__.'/base.php';
require_once __DIR__.'/answerComment.php';
require_once __DIR__.'/reviewer.php';

class Answer  extends Base
{
	private $QID, $string, $answerTimeStamp, $voteUp, $voteDown, $commentList, $commentListType, $suggestionUsedList, $reportAbuseCount, $requestedUser, $alreadyVoted, $reviewerID;

	public function __construct($QID, $string, $timeStamp, $reviewerID){
		parent::__construct();
		$this->QID = $QID;
		$this->string = $string;
		$this->answerTimeStamp = $timeStamp;
		$this->reviewerID = $reviewerID;
		if($this->validateVar($_SESSION) && $this->validateVar($_SESSION['user'])){
			$this->requestedUser = unserialize($_SESSION['user'])->getUserName();
		}else{
			$this->requestedUser = null;
		}


		//fetching Votes
		$db = $this->getDb();
		$db->query("SELECT userName,nature FROM AnswerVotes WHERE QID=? And timeStamp=? And reviewer=?",array($this->QID, $this->answerTimeStamp, $this->reviewerID));
		$alreadyVoted = false;
		$voteUp = 0;
		$voteDown = 0;
		$records = $db->fetch_assoc_all();
		foreach ($records as $key => $value) {
			if($value['nature'] > 0){
				$voteUp += 1;
			}else{
				$voteDown += 1;
			}
			if($value['userName'] == $this->requestedUser){
				$alreadyVoted = true;
			}
		}
		$this->voteUp = $voteUp;
		$this->voteDown = $voteDown;
		$this->alreadyVoted = $alreadyVoted;

		//fetchingComment
		$db->query("SELECT userName,string,timeStamp FROM AnswerComment WHERE QID=? And AnswerTimeStamp=? And reviewerID=?",array($this->QID, $this->answerTimeStamp, $this->reviewerID));
		$commentList = array();
		$records = $db->fetch_assoc_all();
		foreach ($records as $key => $value) {
			array_push($commentList, new AnswerComment($this->QID, $this->reviewerID, $this->answerTimeStamp, $value['userName'], $value['timeStamp'], $value['string']));
		}
		$this->commentList = $commentList;

	}

	public function getQID()
	{
	    return $this->QID;
	}
	
	public function setQID($QID)
	{
	    $this->QID = $QID;
	}
	
	public function getReviewerID()
	{
	    return $this->reviewerID;
	}
	
	public function setReviewerID($reviewerID)
	{
	    $this->reviewerID = $reviewerID;
	}
	
	public function getTimeStamp()
	{
	    return $this->timeStamp;
	}
	
	public function setTimeStamp($timeStamp)
	{
	    $this->timeStamp = $timeStamp;
	}

	public function getString()
	{
	    return $this->string;
	}
	
	public function setString($string)
	{
	    $this->string = $string;
	}
	
	public function getVoteUp()
	{
	    return $this->voteUp;
	}
	
	public function getVoteDown()
	{
	    return $this->voteDown;
	}
	
	/*
	public function getCommentList()
	{
	    return $this->commentList;
	}
	
	public function setCommentListType($commentListType)
	{
	    $this->commentListType = $commentListType;
	}

	public function getCommentListType()
	{
	    return $this->commentListType;
	}

	public function getSuggestionUsedList()
	{
		return $this->suggestionUsedList;
	}
	*/

	public function getReportAbuseCount()
	{
		return $this->reportAbuseCount;
	}

	public function getAlreadyVoted()
	{
	    return $this->alreadyVoted;
	}
	
	public function addComment($commentString)
	{
		$db = $this->getDb();
		$db->query("INSERT INTO userName,nature FROM AnswerVotes WHERE QID=? And timeStamp=? And reviewerID=?",array($this->QID, $this->timeStamp, $this->reviewerID));

	}

	public function toArray(){
		$object = array();
		// $object['QID'] = $this->QID;
		$object['userName'] = $this->reviewerID;
		$object['string'] = $this->string;
		$object['timeStamp'] = $this->answerTimeStamp;
		$object['voteUp'] = $this->voteUp;
		$object['voteDown'] = $this->voteDown;
		$object['alreadyVoted'] = $this->alreadyVoted;
		$object['reportAbuseCount'] = $this->reportAbuseCount;
		$commentsTemp = array();
		foreach ($this->commentList as $key => $value) {
			array_push($commentsTemp, $value->toArray());
		}
		$object['commentList'] = $commentsTemp;
		return ($object);
	}

	public static function compareVoteUp($a, $b){
		return -($a->getVoteUp() - $b->getVoteUp());
	}

	public static function addAnswer($QID, $string){
		$db = (new Database())->connectToDatabase();
		$db->query("SELECT QID FROM Question WHERE QID=$QID");
		if($db->returned_rows == 1){
			$status = $db->query("INSERT INTO Answer VALUES('$QID',?,now(),'$string')", array(unserialize($_SESSION['user'])->getUserName()));
			return $status;	
		}else{
			return false;
		}
	}
}
?>