<?

require_once './initialize_database.php';

class Comment{

	private $userName, $voteValue, $timeStamp, $commentString, $upVote, $downVote, $reportAbuseCount, $db;

	public function __construct($QID, $userName, $timeStamp, $text, $ruserName){
		$this->timeStamp = $timeStamp;
		$db = $this->getDb();
		$db->query("SELECT * FROM QuestionCommentVotes WHERE username=? and ",array($username));
	}

	private function getDb()
	{
		if(!isset($db)){
			return (new Database())->connectToDatabase();
		}else{
			return $this->$db;
		}
	}
}

?>