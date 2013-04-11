<?php

require_once __DIR__.'/questionTitle.php';
require_once __DIR__.'/answer.php';
require_once __DIR__.'/suggestion.php';
require_once __DIR__.'/base.php';
require_once __DIR__.'/questionPromo.php';

class Question extends Base
{
	private $questionTitle, $bestAnswer, $answerList, $suggestionList;

	public function __construct(){
		if(func_num_args() == 6){
			$QID = func_get_arg(0);
			$string = func_get_arg(1);
			$timeStamp = func_get_arg(2);
			$difficultyLevel = func_get_arg(3);
			$userName = func_get_arg(4);
			$reviewer = func_get_arg(5);
		}elseif(func_num_args() == 1){
			$QID = func_get_arg(0);

			$db = $this->getDb();
			$db->query("SELECT string,timeStamp,reviewer,userName,difficultyLevel FROM Question WHERE QID = '$QID'");
			$records = $db->fetch_assoc_all();
			$string = $records[0]['string'];
			$timeStamp = $records[0]['timeStamp'];
			$difficultyLevel = $records[0]['reviewer'];
			$userName = $records[0]['userName'];
			$reviewer = $records[0]['difficultyLevel'];
		}

		parent::__construct();
		
		$this->questionTitle = new QuestionTitle($QID, $string, $timeStamp, $difficultyLevel, $userName, $reviewer);

		//fetching answers
		$db = $this->getDb();
		$db->query("SELECT string,timeStamp,reviewerId FROM Answer WHERE QID = '$QID' ORDER BY timeStamp DESC LIMIT 0, 1");
		$records = $db->fetch_assoc_all();
		
		$this->bestAnswer = null;
		if($db->returned_rows > 0){
			$this->bestAnswer =  new Answer($QID, $records[0]['string'], $records[0]['timeStamp'], $records[0]['reviewerId']);
		}
		$this->answerList = array('type' => DEFAULT_TYPE, 'list' => array());

		$this->suggestionList = array('type' => DEFAULT_TYPE, 'list' => array());
		$this->suggestionList['list'] = $this->getSuggestionList(DEFAULT_TYPE);

	}

	public function getQID()
	{
		return $this->QID;
	}

	public function getAnswerList($type){
		if($type != $this->answerList['type']){
			$this->fetchAnswerList($type);
		}elseif(count($this->answerList['list']) == 0){
			$this->fetchAnswerList();
		}

		return $this->answerList['list'];
	}

	public function getAnswerListArray($type){
		$list = $this->getAnswerList($type);
		$jsonList=array();
		foreach ($list as $key => $value) {
			array_push($jsonList, $value->toArray());
		}
		return $jsonList;
	}

	public function fetchAnswerList($type = DEFAULT_TYPE){
		if($this->typeValidation($type)){
			if($type == $this->answerList['type']){
				$len = count($this->answerList['list']);
			}else{
				$len = 0;
				$this->answerList['type'] = $type;
				$this->answerList['list'] = array();
			}
			//Fetching Questions
			$db = $this->getDb();
			if($type != 'popularity'){
				$db->query("SELECT string,timeStamp,reviewerId FROM Answer WHERE QID = $QID ORDER BY $type DESC LIMIT $len, ".MORE_SIZE);
			}else{
				$db->query("SELECT string,timeStamp,reviewerId FROM Answer WHERE QID = $QID");
			}
			$records = $db->fetch_assoc_all();

			foreach ($records as $key => $value){
				array_push($this->answerList['list'], new Answer($this->QID, $value['string'], $value['timeStamp'], $value['reviewerId']));
			}
			if($type == 'popularity')
			{
				usort($this->answerList['list'], "Answer::compareVoteUp");
				$this->answerList['list'] = array_slice($this->answerList['list'], 0, $len + MORE_SIZE);
			}
						
			$this->result['head']['status'] = 200;
		}else{
			$this->result['head']['status'] = 400;
			$this->result['head']['message'] = 'unkown type';
		}
	}

	public function getSuggestionList($type){
		if($type != $this->suggestionList['type']){
			$this->fetchSuggestionList($type);
		}elseif(count($this->suggestionList['list']) == 0){
			$this->fetchSuggestionList();
		}

		return $this->suggestionList['list'];
	}

	public function getSuggestionListArray($type){
		$list = $this->getSuggestionList($type);
		$jsonList=array();
		foreach ($list as $key => $value) {
			array_push($jsonList, $value->toArray());
		}
		return $jsonList;
	}

	public function fetchSuggestionList($type = DEFAULT_TYPE){
		if($this->typeValidation($type)){
			if($type == $this->suggestionList['type']){
				$len = count($this->suggestionList['list']);
			}else{
				$len = 0;
				$this->suggestionList['type'] = $type;
				$this->suggestionList['list'] = array();
			}
			//Fetching Questions
			$db = $this->getDb();
			if($type != 'popularity'){
				$db->query("SELECT string,userName,timeStamp,reviewerId FROM Suggestion WHERE QID = ? ORDER BY $type DESC LIMIT $len, ".MORE_SIZE, array($this->questionTitle->getQID()));
			}else{
				$db->query("SELECT string,userName,timeStamp,reviewerId FROM Suggestion WHERE QID = ?", array($this->questionTitle->getQID()));
			}
			$records = $db->fetch_assoc_all();

			foreach ($records as $key => $value){
				array_push($this->suggestionList['list'], new Suggestion($this->questionTitle->getQID(), $value['userName'], $value['timeStamp'], $value['string'], is_null($value['reviewerId']), $value['reviewerId']));
			}
			if($type == 'popularity'){
				usort($this->suggestionList['list'], "Suggestion::compareVoteUp");
				$this->suggestionList['list'] = array_slice($this->suggestionList['list'], 0, $len + MORE_SIZE);
			}
						
			$this->result['head']['status'] = 200;
		}else{
			$this->result['head']['status'] = 400;
			$this->result['head']['message'] = 'unkown type';
		}
	}

	public function toArray(){
		$object = array();
		$object['question'] = $this->questionTitle->toArray();
		//echo json_encode($object['question']) ;
		if(is_null($this->bestAnswer)){
			$object['bestAnswer'] = null;
		}else{
			$object['bestAnswer'] = $this->bestAnswer->toArray();
		}
		if(empty($this->suggestionList['list'])){
			$object['suggestions'] = null;
		}else{
			$suggestionListArray = array();
			foreach ($this->suggestionList['list'] as $key => $value) {
				array_push($suggestionListArray, $value->toArray());
			}
			$object['suggestions'] = $suggestionListArray;
		}
		
		return $object;
	}

	public static function getQuestions($type = 'timestamp', $num = 10, $lastQuestionTime = null, $scroll = 'after'){
		$db = (new Database())->connectToDatabase();
		
		$condition = $scroll == 'after' ?'>' : '<'; 

		if($type != 'popularity'){
			if(is_null($lastQuestionTime)){
				$db->query("SELECT * FROM Question ORDER BY $type DESC LIMIT 0,$num");
			}else{
				$db->query("SELECT * FROM Question WHERE timestamp $condition $lastQuestionTime ORDER BY $type DESC LIMIT 0,$num");
			}
		}else{
			if(is_null($lastQuestionTime)){
				$db = query("SELECT * FROM Question");
			}else{
				$db = query("SELECT * FROM Question WHERE timestamp $condition $lastQuestionTime");
			}
		}

		$list = array();

		$records = $db->fetch_assoc_all();
		foreach ($records as $key => $value) {
			array_push($list, new Question($value['QID'], $value['string'], $value['timeStamp'], $value['difficultyLevel'], $value['userName'], $value['reviewer']));
		}

		if($type == 'popularity'){
			usort($list, "Question::compareVoteUp");
			$list = array_slice($list, 0, $num);
		}

		$jsonList = array();
		foreach ($list as $key => $value) {
			array_push($jsonList, $value->toArray());
		}

		return $jsonList;
	}

	public static function generateQID(){
		$db = (new Database())->connectToDatabase();
		$db->query("SELECT MAX(CAST( QID AS SIGNED )) as maxQID FROM Question");
		$records = $db->fetch_assoc_all();
		$latestQID = $records[0]['maxQID'];
		if($records[0]['maxQID'] == NULL){
			$latestQID = 0;
		}
		else{
			$latestQID = $latestQID + 0;
			$latestQID = $latestQID + 1;
		}
		return $latestQID;
	}

	/*public static function addQuestion($assignQID,$newString,$difficultyLevel,$userName){
		$db = $this->getDb();
		$db->query("INSERT INTO Question (QID, string, difficultyLevel, userName) VALUES ('$assignQID', 'newString', '$difficultyLevel', '$userName')");
		// Ask how to check if query successfully completed or not.
	}*/

	public static function addQuestion(array $data){
		$db = (new Database())->connectToDatabase();
		$check = $db->query("INSERT INTO Question (QID, string, difficultyLevel, userName) VALUES(?,?,?,?)", $data);
		return $check;
	}

	public static function findTags($tag){
		$tag = strtolower($tag);
		$db = (new Database())->connectToDatabase();
		$check = $db->query("SELECT name FROM Tags WHERE name='$tag'");
		if($db->returned_rows <= 0)
		{
			return FALSE;
		}
		else{
			return TRUE;
		}
	}

	public static function addQuestionTags($tag, $assignQID){
		$tag = strtolower($tag);
		$db = (new Database())->connectToDatabase();
		$check = $db->query("INSERT INTO Encompass (tagName, QID) VALUES ('$tag', '$assignQID')");
		return $check;
	}

	public static function addVote($QID, $userName, $nature)
	{
		$db = (new Database())->connectToDatabase();
		$status = $db->query("INSERT INTO QuestionVotes (QID, userName, nature) VALUES ('$QID', '$userName', '$nature')");
		return $status;
	}

	public static function checkAlreadyVoted($QID, $userName)
	{
		$db = (new Database())->connectToDatabase();
		$records = $db->query("SELECT userName FROM QuestionVotes WHERE QID='$QID' AND userName='$userName'");
		if($db->returned_rows > 0){
			$name = $db->fetch_assoc_all()[0]['userName'];
		}
		else{
			$name = NULL;
		}
		return $name;
	}

	public static function checkVoteNature($QID, $userName)
	{
		$db = (new Database())->connectToDatabase();
		$db->query("SELECT nature FROM QuestionVotes WHERE QID='$QID' AND userName='$userName'");
		$nature = $db->fetch_assoc_all()[0]['nature'];
		return $nature;
	}

	public static function updateVote($QID, $userName, $nature)
	{
		$db = (new Database())->connectToDatabase();
		$status = $db->query("UPDATE QuestionVotes SET nature=$nature WHERE QID='$QID' AND userName='$userName'");
		return $status;
	}

	public static function compareVoteUp($a, $b){
		return $b->getQuestionTitle()->getVoteUp() - $a->getQuestionTitle()->getVoteUp();
	}

	public static function searchTag($tag){
		$db = (new Database())->connectToDatabase();
		$db->query("SELECT * FROM Question NATURAL JOIN (SELECT QID FROM Encompass WHERE tagName='$tag')as q");
		$records = $db->fetch_assoc_all();
		$object = array();
		foreach ($records as $key => $value) {
			array_push($object, (new QuestionPromo($value['QID'], $value['userName'], $value['string'], $value['timeStamp'], $value['difficultyLevel']))->toArray());
		}

		return $object;
	}

	public static function topTenTags()
	{
		$db = (new Database())->connectToDatabase();
		$db->query("SELECT COUNT( QID ) AS count_qid, tagName FROM Encompass GROUP BY tagName ORDER BY count_qid DESC LIMIT 10");
		$records = $db->fetch_assoc_all();
		$object = array(); 
		foreach ($records as $key => $value) {
			array_push($object, $value['tagName']);
		}
		return $object;
	}

	public static function reviewQuestion($QID, $string, $tags, $difficultyLevel){
		$db = (new Database())->connectToDatabase();
		$flag = true;
		foreach ($tags as $key => $value) {
			$db->query("SELECT name From Tags WHERE name='$value'");
			if($db->returned_rows != 1){
				$flag = false;
				break;
			}
		}
		if($flag){
			if($db->query("DELETE FROM Encompass WHERE QID=$QID")){
				foreach ($tags as $key => $value) {
					if(!$db->query("INSERT INTO Encompass VALUES('$value', '$QID')")){
						$result = array("head" => array("status" => 409, "message" => 'Unable to add tags'), "body" => '');
						return $result;
					}
				}
				if($db->query("UPDATE Question SET string='$string', difficultyLevel=$difficultyLevel, locked=0, reviewer=? WHERE QID='$QID'", array(unserialize($_SESSION['user'])->getUserName() ))){
					$result = array("head" => array("status" => 200, "message" => ''), "body" => '');
				}else{
					$result = array("head" => array("status" => 409, "message" => 'Error modifying question'), "body" => '');
				}
			}			
		}else{
			$result = array("head" => array("status" => 403, "message" => "Undefined(Wrong) Tags"), "body" => '');
		}
		return $result;	
	}

}
?>