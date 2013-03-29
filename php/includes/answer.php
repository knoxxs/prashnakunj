<?php

class Answer  extends Base
{
	private $QID, $string, $timeStamp, $voteUp, $voteDown, $commentList, $commentListType, $suggestionUsedList, $reportAbuseCount, $requestedUser, $alreadyVoted, $reviewerID;

	public function __construct($QID, $string, $timeStamp, $reviewerID){
		$this->QID = $QID;
		$this->string = $string;
		$this->timeStamp = $timeStamp;
		$this->reviewerID = $reviewerID;
		$this->requestedUser = unserialize($_SESSION['user'])->getUserName();

		//fetching Votes
		$db = $this->getDb();
		$db->query("SELECT userName,nature FROM AnswerVotes WHERE QID=?",array($QID));
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
	
	public function getCommentList()
	{
	    return $this->commentList;
	}
	
	public function setCommentListType($commentListType)
	{
	    $this->commentListType = $commentListType;
	}

	public function getCommentListType($commentListType)
	{
	    return $this->commentListType;
	}

	public function getAlreadyVoted()
	{
	    return $this->alreadyVoted;
	}
	
	public

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

		return ($object);
	}

	public static function compareVoteUp($a, $b){
		return -($a->getVoteUp() - $b->getVoteUp());
	}


}
?>
}

?>