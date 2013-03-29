<?php

class Question extends Base
{
	private $questionTitle, $bestAnswer, $answerList, $suggestionList;

	public function __construct($QID, $string, $timeStamp, $difficultyLevel, $userName, $reviewer){
		$this->questionTitle = new QuestionTitle($QID, $string, $timeStamp, $difficultyLevel, $userName, $reviewer);

		//fetching answers
		$db = $this->getDb();
		$db->query("SELECT string,timeStamp,reviewerId FROM Answer WHERE QID = $QID ORDER BY timeStamp DESC LIMIT 0, 1");

	}

	public function toArray(){
		$object = array();
		$object['QID'] = $this->QID;
		$object['userName'] = $this->userName;
		$object['string'] = $this->string;
		$object['timeStamp'] = $this->timeStamp;
	}

	public static function getQuestions($type = 'timestamp', $num = 10, $lastQuestionTime = null, $scroll = 'after'){
		$db = (new Database())->connectToDatabase();
		
		$condition = $scroll == 'after' ?'>' : '<'; 

		if($type != 'popularity'){
			if(is_null($lastQuestionTime)){
				$db = query("SELECT * FROM Question ORDER BY $type DESC LIMIT 0,$num");
			}else{
				$db = query("SELECT * FROM Question WHERE timestamp $condition $lastQuestionTime ORDER BY $type DESC LIMIT 0,$num");
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

	public static function compareVoteUp($a, $b){
		return $b->getQuestionTitle()->getVoteUp() - $a->getQuestionTitle()->getVoteUp();
	}
}


?>