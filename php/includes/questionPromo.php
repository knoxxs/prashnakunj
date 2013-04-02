<?php

class QuestionPromo extends Base
{ 
	private $QID, $userName, $string, $timeStamp, $voteUp, $voteDown, $difficultyLevel, $tagList, $alreadyVoted, $alreadyFav, $requestedUser;

	public function __construct($QID, $userName, $string, $timeStamp, $difficultyLevel){
		parent::__construct();
		$this->QID = $QID;
		$this->userName = $userName;
		$this->string = $string;
		$this->timeStamp = $timeStamp;
		$this->difficultyLevel = $difficultyLevel;
		if($this->validateVar($_SESSION['user'])){
			$this->requestedUser = unserialize($_SESSION['user'])->getUserName();
		}else{
			$this->requestedUser = null;
		}

		//fetching Votes
		$db = $this->getDb();
		$db->query("SELECT userName,nature FROM QuestionVotes WHERE QID=?",array($QID));
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
		$records = $db->fetch_assoc_all();
		foreach ($records as $key => $value) {
			array_push($tagList, $value['tagName']);
		}
		$this->tagList = $tagList;

		//fetching is alreadyFavourited
		$db->query('SELECT QID FROM Favourites WHERE QID=? AND userName=?',array($this->QID, $this->requestedUser));
		if($db->returned_rows == 1){
			$this->alreadyFav = true;
		}else{
			$this->alreadyFav = false;
		}
	}

	public function __destruct(){

	}

	/**
	 * object summary
	 * @return string [description]
	 */
	public function __toString(){
		return print_r($this);
	}

	public function getQID()
	{
	    return $this->QID;
	}
	
	public function setQID($QID)
	{
	    $this->QID = $QID;
	}
	
	public function getUserName()
	{
	    return $this->userName;
	}
	
	public function setUserName($userName)
	{
	    $this->userName = $userName;
	}
	
	public function getString()
	{
	    return $this->string;
	}
	
	public function setString($string)
	{
	    $this->string = $string;
	}
	
	public function getTimeStamp()
	{
	    return $this->timeStamp;
	}
	
	public function setTimeStamp($timeStamp)
	{
	    $this->timeStamp = $timeStamp;
	}
	
	public function getVoteUp()
	{
	    return $this->voteUp;
	}
	
	public function setVoteUp($voteUp)
	{
	    $this->voteUp = $voteUp;
	}
	
	public function getVoteDown()
	{
	    return $this->voteDown;
	}
	
	public function setVoteDown($voteDown)
	{
	    $this->voteDown = $voteDown;
	}

	public function getDifficultyLevel()
	{
	    return $this->difficultyLevel;
	}
	
	public function setDifficultyLevel($difficultyLevel)
	{
	    $this->difficultyLevel = $difficultyLevel;
	}
	
	public function getTagList()
	{
	    return $this->tagList;
	}
	
	public function setTagList($tagList)
	{
	    $this->tagList = $tagList;
	}

	public function getAlreadyVoted()
	{
	    return $this->alreadyVoted;
	}
	
	public function setAlreadyVoted($alreadyVoted)
	{
	    $this->alreadyVoted = $alreadyVoted;
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

		return ($object);
	}

	public static function compareVoteUp($a, $b){
		return -($a->getVoteUp() - $b->getVoteUp());
	}


}
?>