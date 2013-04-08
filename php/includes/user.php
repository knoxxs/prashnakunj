<?php
require_once __DIR__.'/questionPromo.php';
require_once __DIR__.'/base.php';

class User extends Base{ 
	protected $userName, $firstname, $lastname, $reputation, $favList, $watchLaterList, $historyList, $subscriptionList;

	/**
	 * [__construct description]
	 * @param [type] $userName [description]
	 */
	public function __construct($userName){
		parent::__construct();

		$this->userName = $userName;
		$db = $this->getDb();
		$db->query("SELECT firstname, lastname,reputation FROM User WHERE userName=?",array($userName));
		$records = $db->fetch_assoc_all();
		$this->firstname = $records[0]['firstname'];
		$this->lastname = $records[0]['lastname'];
		$this->reputation = $records[0]['reputation'];
		
		$this->watchLaterList['type'] = DEFAULT_TYPE;
		$this->watchLaterList['list'] = array();
		//$this->fetchWatchLaterList();
		
		$this->favList['type'] = DEFAULT_TYPE;
		$this->favList['list'] = array();
		//$this->fetchFavList();

		$this->historyList['type'] = DEFAULT_TYPE;
		$this->historyList['list'] = array();
		//$this->fetchHistoryList();

		$this->subscriptionList = array();
		//$this->fetchSubscriptionList();
	}

	public function getUsername(){
		return $this->userName;
	}
	
	public function getFirstname(){
		return $this->firstname;
	}
	
	public function getLastname(){
		return $this->lastname;
	}					

	public function getFavList($type){
		if($type != $this->favList['type']){
			$this->fetchFavList($type);
		}elseif(count($this->favList['list']) == 0){
			$this->fetchFavList();
		}

		return $this->favList['list'];
	}

	public function getFavListArray($type){
		$list = $this->getFavList($type);
		$jsonList=array();
		foreach ($list as $key => $value) {
			array_push($jsonList, $value->toArray());
		}
		return $jsonList;
	}

	public function getWatchLaterList($type){
		if($type != $this->watchLaterList['type']){
			$this->fetchWatchLaterList($type);
		}elseif(count($this->watchLaterList['list']) == 0){
			$this->fetchWatchLaterList();
		}

		return $this->watchLaterList['list'];
	}

	public function getWatchLaterListArray($type){
		$list = $this->getWatchLaterList($type);
		$jsonList=array();
		foreach ($list as $key => $value) {
			array_push($jsonList, $value->toArray());
		}
		return $jsonList;
	}

	public function getHistoryList($type){
		if($type != $this->historyList['type']){
			$this->fetchHistoryList($type);
		}elseif(count($this->historyList['list']) == 0){
			$this->fetchHistoryList();
		}

		return $this->historyList['list'];
	}

	public function getHistoryListArray($type){
		$list = $this->getHistoryList($type);
		$jsonList=array();
		foreach ($list as $key => $value) {
			array_push($jsonList, $value->toArray());
		}
		return $jsonList;
	}

	public function getSubscriptionList(){
		if(count($this->subscriptionList) == 0){
			$this->fetchSubscriptionList();
		}
		return $this->subscriptionList;
	}

	public function getReputation(){
		return $this->reputation;
	}
	
	public function setReputation($reputation){
		$this->reputation = $reputation;
		return ;
	}

	public function addFavItem($item){

	}

	public function addWatchLaterItem($item){
		
	}

	public function addHistoryItem($item){
		
	}

	public function addSubscriptionItem($item){
		
	}	

	/**
	 * fetch watchLaterList more items based on sortType
	 * @param  string $type [description]
	 * @return int       reuqest status
	 */
	public function fetchWatchLaterList($type = DEFAULT_TYPE){
		if($this->typeValidation($type)){
			if($type == $this->watchLaterList['type']){
				$len = count($this->watchLaterList['list']);
			}else{
				$len = 0;
				$this->watchLaterList['type'] = $type;
				$this->watchLaterList['list'] = array();
			}
			//Fetching Questions
			$db = $this->getDb();
			//SELECT SUM(nature),COUNT(nature) FROM (SELECT qid,string,timeStamp,difficultyLevel FROM Question NATURAL JOIN (SELECT qid FROM Watch WHERE userName='uname2') as W ORDER BY timestamp LIMIT 0,5) as Q JOIN QuestionVotes as QV ON Q.QID=QV.QID GROUP BY Q.QID
			if($type != 'popularity')
			{
				$db->query("SELECT QID,userName,string,timeStamp,difficultyLevel FROM Question NATURAL JOIN (SELECT qid FROM Watch WHERE userName=?) as W ORDER BY " . $type . " LIMIT " . $len . "," . MORE_SIZE , array($this->userName));
			}
			else
			{
				$db->query("SELECT QID,userName,string,timeStamp,difficultyLevel FROM Question NATURAL JOIN (SELECT qid FROM Watch WHERE userName=?) as W ", array($this->userName));
			}
			$records = $db->fetch_assoc_all();

			foreach ($records as $key => $value){
				array_push($this->watchLaterList['list'], new QuestionPromo( $value['QID'], $value['userName'], $value['string'], $value['timeStamp'], $value['difficultyLevel']) );
			}
			if($type == 'popularity')
			{
				usort($this->watchLaterList['list'], "QuestionPromo::compareVoteUp");
				$this->watchLaterList['list'] = array_slice($this->watchLaterList['list'], 0, $len + MORE_SIZE);
			}

			$this->result['head']['status'] = 200;
		}else{
			$this->result['head']['status'] = 400;
			$this->result['head']['message'] = 'unkown type';
		}
	}

	public function fetchFavList($type = DEFAULT_TYPE){
		if($this->typeValidation($type)){
			if($type == $this->favList['type']){
				$len = count($this->favList['list']);
			}else{
				$len = 0;
				$this->favList['type'] = $type;
				$this->favList['list'] = array();
			}
			//Fetching Questions
			$db = $this->getDb();
			if($type != 'popularity')
			{
				$db->query("SELECT QID,userName,string,timeStamp,difficultyLevel FROM Question NATURAL JOIN (SELECT qid FROM Favourites WHERE userName=?) as W ORDER BY " . $type . " LIMIT " . $len . "," . MORE_SIZE , array($this->userName));
			}
			else
			{
				$db->query("SELECT QID,userName,string,timeStamp,difficultyLevel FROM Question NATURAL JOIN (SELECT qid FROM Favourites WHERE userName=?) as W ", array($this->userName));
			}
			$records = $db->fetch_assoc_all();

			foreach ($records as $key => $value){
				array_push($this->favList['list'], new QuestionPromo( $value['QID'], $value['userName'], $value['string'], $value['timeStamp'], $value['difficultyLevel']) );
			}
			if($type == 'popularity')
			{
				usort($this->favList['list'], "QuestionPromo::compareVoteUp");
				$this->favList['list'] = array_slice($this->favList['list'], 0, $len + MORE_SIZE);
			}
						
			$this->result['head']['status'] = 200;
		}else{
			$this->result['head']['status'] = 400;
			$this->result['head']['message'] = 'unkown type';
		}
	}

	public function fetchHistoryList($type = DEFAULT_TYPE){
		if($this->typeValidation($type)){
			if($type == $this->historyList['type']){
				$len = count($this->historyList['list']);
			}else{
				$len = 0;
				$this->historyList['type'] = $type;
				$this->historyList['list'] = array();
			}
			//Fetching Questions
			$db = $this->getDb();
			if($type != 'popularity')
			{
				$db->query("SELECT QID,userName,string,timeStamp,difficultyLevel FROM Question NATURAL JOIN (SELECT qid FROM Views WHERE userName=?) as W ORDER BY " . $type . " LIMIT " . $len . "," . MORE_SIZE , array($this->userName));
			}
			else
			{
				$db->query("SELECT QID,userName,string,timeStamp,difficultyLevel FROM Question NATURAL JOIN (SELECT qid FROM Views WHERE userName=?) as W ", array($this->userName));
			}
			$records = $db->fetch_assoc_all();

			foreach ($records as $key => $value){
				array_push($this->historyList['list'], new QuestionPromo( $value['QID'], $value['userName'], $value['string'], $value['timeStamp'], $value['difficultyLevel']) );
			}
			if($type == 'popularity')
			{
				usort($this->historyList['list'], "QuestionPromo::compareVoteUp");
				$this->historyList['list'] = array_slice($this->historyList['list'], 0, $len + MORE_SIZE);
			}

			$this->result['head']['status'] = 200;
		}else{
			$this->result['head']['status'] = 400;
			$this->result['head']['message'] = 'unkown type';
		}
	}

	public function fetchSubscriptionList(){
		$db = $this->getDb();
		$db->query('SELECT tagName FROM Subscribe WHERE userName=?',array($this->userName));
		$tagList = array();
		$records = $db->fetch_assoc_all();
		foreach ($records as $key => $value) {
			array_push($tagList, $value['name']);
		}
		$this->subscriptionList = $tagList;
		$this->result['head']['status'] = 200;
	}

	/**
	 * Returns the complete profile object for the user
	 * @return Profile Object A object which contain the whole profile of the user
	 */
	public function getProfile(){
		return (new Profile($this->userName));
	}


	//TODO: Need to check password before
	/**
	 * Delete a user from the database
	 * Usage Example: echo User::del('uname');
	 * @param  [type] $userName userName of the user to delete
	 * @return [type]           True or False
	 */
	public static function del($userName){
		return (new Database())->connectToDatabase()->query('DELETE FROM user WHERE userName=?',array($userName));
	}
}
