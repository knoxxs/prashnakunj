<?

class QuestionPromo extends Base
{ 
	private $QID, $userName, $string, $timeStamp, $voteUp, $voteDown, $difficultyLevel, $tagList, $alreadyVoted, $requestedUser;

	public function __construct($QID, $userName, $string, $timeStamp, $difficultyLevel){
		$this->QID = $QID;
		$this->userName = $userName;
		$this->string = $string;
		$this->timeStamp = $timeStamp;
		$this->difficultyLevel = $difficultyLevel;
		$this->requestedUser = $_SESSION['user']->getUserName();

		//fetching Votes
		$db = $this->getDb();
		$db->query("SELECT userName,nature FROM QuestionVotes WHERE QID=?",array($QID));
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

		//fetching Tags
		$db->query('SELECT tagName FROM Encompass WHERE QID=?',array($QID));
		$tagList = array();
		$records2 = $db->fetch_assoc_all();
		foreach ($records2 as $key2 => $value2) {
			array_push($tagList, $value2['tagName']);
		}
		$this->tagList = $tagList;
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
}
?>