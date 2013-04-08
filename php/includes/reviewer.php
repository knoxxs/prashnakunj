<?php
require_once __DIR__.'/user.php';
require_once __DIR__.'/base.php';

class Reviewer extends User{

	protected $reviewHistoryList, $toBeReviewList;

	public function __construct($userName){
		parent::__construct($userName);

		$this->fetchSubscriptionList();
		$this->reviewHistoryList = array();
		$this->toBeReviewList = array();

	}

	public function getReviewHistoryList(){
		if(count($this->reviewHistoryList) == 0){
			$this->fetchReviewHistoryList();
		}
		return $this->reviewHistoryList;
	}

	public function getReviewHistoryListArray(){
		$list = $this->getReviewHistoryList();
		$jsonList=array();
		foreach ($list as $key => $value) {
			array_push($jsonList, $value->toArray());
		}
		return $jsonList;
	}

	public function fetchReviewHistoryList(){
		$len = count($this->reviewHistoryList);
		//Fetching Questions
		$db = $this->getDb();
		$db->query("SELECT QID,suggestionUserName,suggestionTimeStamp,timeStamp FROM ReviewHistory WHERE reviewerId=? ORDER BY timeStamp LIMIT " . $len . "," . MORE_SIZE , array($this->userName));
		$records = $db->fetch_assoc_all();

		foreach ($records as $key => $value){
			array_push($this->reviewHistoryList, new ReviewHistory( $value['QID'], $value['suggestionUserName'], $value['suggestionTimeStamp'], $this->userName, $value['timeStamp']) );
		}					
		$this->result['head']['status'] = 200;
	}

	public function getToBeReviewList(){
		if(count($this->toBeReviewList) == 0){
			$this->fetchToBeReviewList();
		}
		return $this->toBeReviewList;
	}

	public function getToBeReviewListArray(){
		$list = $this->getToBeReviewList();
		$jsonList=array();
		foreach ($list as $key => $value) {
			array_push($jsonList, $value->toArray());
		}
		return $jsonList;
	}

	public function fetchToBeReviewList(){
		$len = count($this->toBeReviewList);

		//Fetching Questions
		$db = $this->getDb();
		foreach($this->tagList as $key => $value){
			//TODO: REMOVE those notifications which are already locked
			$db->query("SELECT QID,suggestionUserName,suggestionTimeStamp FROM Comprehend WHERE name=? AND parent=?", array($value['name'], $value['parent']));
			$records = $db->fetch_assoc_all();
			foreach ($records as $key2 => $value2){
				array_push($this->toBeReviewList, new Review( $value2['QID'], $value2['suggestionUserName'], $value2['suggestionTimeStamp']) );
			}					
		}

		$this->result['head']['status'] = 200;
	}
} 
